<?php

namespace App\Console\Commands\Migrate;

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
    protected $signature = 'ark:migrate:blocks {height=0} {--skip}';

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
        $pages = $this->getPages();

        for ($i = 0; $i < $pages; $i++) {
            $blocks = $this->getBlocks($i);

            foreach ($blocks as $block) {
                if ($block['generator']['publicKey'] !== config('delegate.publicKey')) {
                    continue;
                }

                try {
                    Block::where('block_id', $block['id'])->firstOrFail();
                } catch (\Exception $e) {
                    if ($this->option('skip') && $this->shouldBeSkipped($block)) {
                        $this->line('Skipping older blocks');
                        break 2;
                    }

                    $block = Block::create([
                        'block_id'     => $block['id'],
                        'height'       => $block['height'],
                        'reward'       => $block['forged']['reward'],
                        'fee'          => $block['forged']['fee'],
                        'forged_at'    => humanize_epoch($block['timestamp']['epoch']),
                        'processed_at' => humanize_epoch($block['timestamp']['epoch']),
                    ]);

                    if ($this->shouldBeProcessed($block)) {
                        $this->line('Processing Block: <info>'.$block['height'].'</info>');

                        ProcessBlock::dispatch($block)->onQueue('blocks');
                    } else {
                        $this->line('Indexing Block: <info>'.$block['height'].'</info>');
                    }
                }
            }
        }
    }

    private function getPages(): int
    {
        $count = $this->client->get('blocks', [
            'generatorPublicKey' => config('delegate.publicKey'),
            'limit'              => 1,
        ])['meta']['totalCount'];

        return ceil($count / 100);
    }

    private function getBlocks(int $page): array
    {
        return $this->client->get('blocks', [
            'generatorPublicKey' => config('delegate.publicKey'),
            'offset'             => 100 * $page,
            'limit'              => 100,
            'orderBy'            => 'height:desc',
        ])['data'];
    }

    private function shouldBeProcessed(Block $block): bool
    {
        if ($this->argument('height') <= 0) {
            return false;
        }

        return $block->height >= (int) $this->argument('height');
    }

    private function shouldBeSkipped(array $block): bool
    {
        if ($this->argument('height') <= 0) {
            return false;
        }

        return $block['height'] < (int) $this->argument('height');
    }
}
