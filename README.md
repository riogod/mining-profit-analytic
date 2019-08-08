# mining-profit-analytic
Grabbing data from pools, exchange and cryptocurrency prices for calculating mining profit.

<img src="https://i.imgur.com/mleb9Kl.png">

## Installation
You need to add to your cron next files:

- get_updateEx.php - for parse data from exchanges / every 1H
- pools/get_update.php - for parsing pools data / every 30min
- pools/get_reward.php - for parsing pools coin reward / every 30min

For grabbing data from cryptocurrency indexes add in your cron next files:
- UpdatePrices.php - worldcoinindex
- updatePricesCoinlib.php - Coinlib.io
- UpdatePricesT.php - coinmarketcap

In index.php you need to add your algo hashrates.

Systems need a short period to grab statistics, it's about 6-7 hours, and after past time you can sort profit algo/coins based on your hashrates.
