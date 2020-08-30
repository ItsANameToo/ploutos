# Ploutos

> A True Block Weight (TBW) payout script implementation for ARK

## Features

The most notable features of this payout script are listed below:

- True Block Weight calculation
- Configurable payout percentage
- Configurable delegate payout address and vendor field messages
- Whitelists
- Blacklists
- Payout address different from voting address
- Custom payout percentages per address
- Payout threshold
- Distributing pending balances from blacklisted wallets to active voters

## Requirements

- PHP 7.3+
- [Composer](https://getcomposer.org/download/)
- [Laravel](https://laravel.com)

## Installation

```bash
# Fetch TBW script
git clone git@github.com:ItsANameToo/ploutos.git
cd ploutos

# Setup
composer install
cp .env.example .env
php artisan key:generate
# Encrypt passphrase(s) with tinker
php artisan tinker
encrypt('first passphrase') # copy the encrypted passphrase to your .env file
encrypt('second passphrase') # copy the encrypted passphrase to your .env file
# Edit the rest of your .env file based on the variables in the next section
# If you want to blacklist certain addresses, make sure to add them to the blacklist array in the delegates.php config file!

# Initializing TBW
php artisan migrate:fresh
# Poll current voters
php artisan ark:poll:voters
# Calculate payouts based on the blockheight that you last paid out from (height). Use 1 for height when starting from scratch!
# Use --skip if you are not interested in polling earlier transactions
php artisan ark:migrate:blocks {height=0} {--skip}
# Check current pending balances of your voters
php artisan ark:earnings
```

You need to make sure that the script runs in near real-time to properly fetch block information. The simplest way to do this is by setting a cronjob that calls the script every minute. Laravel itself has a `Kernel` file that further defines which commands will be triggered when the cronjob gets executed.

An example to do this with `crontab -e` would be do add the following line to call the script every minute:

```bash
* * * * * cd ~/ploutos && php artisan schedule:run >> /dev/null 2>&1
```

### Compendia Notes

A few thing to pay attention to when running this script on the Compendia Network:

- Make sure to add any wallets to your `blacklist` that you want to exclude from calculations before fetching your blocks (for example if you're backed by a genesis wallet).
- Starting height needs to be 1 or a variable block height from which you started forging
- Leave the `VENDOR_FIELD` variables in your `.env` file empty as Compendia does not support smartbridge messages!
- set your Multipayment Fee to `25000000`

## Environment

Note that all environment variables are prefixed with `ARK_DELEGATE_` but for the sake of saving space only the last part is included in the below table (they are already in the `.env.example` file anyway)

| Variable | Description | Example | Default |
|----------|-------------|---------|---------|
| NETWORK | The network you run the script on. See the supported networks section for more information. | `devnet` | `mainnet` |
| HOST | The host used to fetch API data from | `"http:1.1.1.1:4003/api/"` | none |
| USERNAME | The username of your delegate | `ItsANameToo` | none |
| ADDRESS | The address of your delegate | `DBk4cPY...NHxpMg8` | none |
| PUBLIC_KEY | The public key of your delegate | `0236d5232...837231d5f0` | none |
| VENDOR_FIELD | The vendor field message that voters will see | `"Voter Share"` | none |
| SHARE_PERCENTAGE | The share percentage (1-100) the voters get | `50` | `90` |
| THRESHOLD | A pending balance threshold above which payments occur to voters (in ARK) | `0.001` | `0.1` |
| PASSPHRASE | The passphrase of your delegate (encrypted) | `"laravel encrypted passphrase"` | none |
| SECOND_PASSPHRASE | The second passphrase of your delegate (encrypted) | `"laravel encrypted passphrase"` | none |
| PERSONAL_ADDRESS | The address where your delegate share should go | `DBk4cPY...NHxpMg8` | none |
| PERSONAL_SHARE_PERCENTAGE | The share percentage you receive as delegate | `50` | none |
| PERSONAL_VENDOR_FIELD | The vendor field message that your delegate payment will contain | `"Delegate share"` | none |
| FEE_COVER | Whether you cover transaction fees for voters | `true` | `false` |
| FEE_DEDUCT | Whether you deduct transaction fees from your delegate's share calculation | `true` | `false` |
| FEE_SHARE | Whether you share fees that you forged in addition to block rewards | `true` | `false` |
| FEE_TRANSFER | The fee used for transfers made by the script | `10000000` | `10000000` (0.1) |
| FEE_TRANSFER | The fee used for transfers made by the script | `25000000` | `10000000` (0.1) |
| STAKING | Whether pending balances are taken into account when calculating payments | `false` | `false` |
| DISTRIBUTE_BLACKLIST | Whether the pending balances of blacklisted wallets gets redistributed amongst active voters | `false` | `false` |
| BROADCAST_TYPE | Whether you want to send your transactions so a single host ("default") or through random peers on the network ("spread") | `"spread"` | `"default"` |
| POLL_TRANSACTIONS | Whether you want to poll payment transactions and save these to the database (e.g. useful for historical data) | `false` | `false` |

## Supported Networks

You can configure the network to run the script on by specifying one of the following values in your `.env` file for the `ARK_DELEGATE_NETWORK` variable:

- `mainnet`: the default value; this will configure the script for the ARK Mainnet
- `devnet`: this will configure the script for the ARK Devnet
- `mainnet_compendia`: this will configure the script for the Compendia (BIND) Mainnet
- `devnet_compendia`: this will configure the script for the Compendia (BIND) Devnet

## Usage

The script will poll for new blocks every 5 minutes and applies calculations when your delegate has forged a new block. There should be no need to manually intervene with the regular payout schedule after setting it up.

It is possible to whitelist (= include address in calculations) or blacklist (= ignore address in calculations) addresses by adding them to the array in the `config/delegate.php` file.

Furthermore there are a couple of commands available that you can make use of, which are explained in the next section.

## Commands

Commands can be run by prepending `php artisan` before the commands, e.g. `php artisan ark:earnings` to show the current pending balances of your voters. Parameters with a default value (or ending in a `?`) are optional, e.g. `php artisan ark:broadcast` and `php artisan ark:broadcast 100` will result in the same action.

| Command | Description |
|----------|-------------|
| `ark:broadcast {number=100}` | Broadcasts the last `<number>` of transactions in the database, useful for rebroadcasting a payment run |
| `ark:earnings` | Show the current pending balances of your voters |
| `ark:earnings:delegate:clear` | Clear the cached delegate earnings (does not reset voter pending balances) |
| `ark:send {amount} {recipient} {--smartbridge=}` | Send a transfer with the specified amount to the specified recipient with an optional smartbridge message |
| `ark:voter:payoutAddress {address} {payoutAddress?}` | Set a different payout address for the specified voter. Leave second parameter out to reset to default |
| `ark:voter:percentage {address} {percentage?}` | Set a different payout percentage for the specified voter. Leave second parameter out to reset to default |
| `php artisan ark:disburse:voters` | Perform a manual voter payout based on the current pending voter balances |
| `php artisan ark:disburse:developer` | Perform a manual delegate payout based on the current pending delegate balance |

## Disclaimer

Please note that it's up to the delegate itself to properly and thoroughly test this TBW payout script before using it in a production environment. It is adviced to try the script on devnet to get an idea of how it works and how it responds in case of errors. There will be no babysitting in case of errors, as you are smart enough to test things beforehand and try them out, right?

## Credits

- [Brian Faust](https://github.com/faustbrian)
- [ItsANameToo](https://github.com/ItsANameToo)
- [All Contributors](../../contributors)

## License

[MIT](LICENSE) © [ItsANameToo](https://itsadelegatetoo.com)
