<?php

namespace App\Console\Commands\Polling;

use App\Jobs\ProcessBlock;
use App\Models\Block;
use App\Services\Ark\Client;
use Illuminate\Console\Command;

class Blocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:poll:blocks {pages=1}';

    public function __construct(Client $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        for ($i = 0; $i < $this->argument('pages'); $i++) {
            $blocks = $this->getBlocks($i);

            foreach ($blocks as $block) {
                if ($block['generatorPublicKey'] !== config('delegate.publicKey')) {
                    continue;
                }

                try {
                    Block::where('block_id', $block['id'])->firstOrFail();
                } catch (\Exception $e) {
                    $this->line('Processing Block: <info>'.$block['id'].'</info>');

                    $block = Block::create([
                        'block_id'  => $block['id'],
                        'height'    => $block['height'],
                        'fee'       => $block['totalFee'],
                        'reward'    => $block['reward'],
                        'forged_at' => humanize_epoch($block['timestamp']),
                    ]);

                    ProcessBlock::dispatch($block)->onQueue('blocks');
                }
            }
        }
    }

    private function getBlocks(int $page): array
    {
        return $this->client->get('api/blocks', [
            'generatorPublicKey' => config('delegate.publicKey'),
            'offset'             => 100 * $page,
            'limit'              => 100,
            'orderBy'            => 'height:desc',
        ])['blocks'];
    }
}
