# CBR currency rates for OhMyShares

Provides daily exchange rates of the [Central Bank of Russia](https://www.cbr.ru/currency_base/daily/) (base currency `RUB`)
to OhMyShares. OhMyShares stores rates locally and converts amounts between any currencies listed by CBR
(cross rates are calculated through `RUB`).

Requires OhMyShares with plugin SDK `ohmyshares/plugin` ≥ 1.2 (`CurrencyRatesProviderInterface`).

## Installation

1. Open `/plugins` page of your OhMyShares instance.
2. Paste `https://github.com/ohmyshares/cbr-currency-rates-plugin` into *Source* text field.
3. Press *Install* button.

Rates are requested hourly by OhMyShares (`php artisan currency:update`), run the command manually to load them right after installation.

## How it works

- `Client` downloads `https://www.cbr.ru/scripts/XML_daily.asp?date_req=dd/mm/YYYY`.
- `Parser` converts the XML into `Dtos\CurrencyRate` items: `rate = Value / Nominal`, `date` — the actual date of rates returned by CBR
  (on weekends and holidays CBR responds with rates of the closest previous date).

## Structure

```
src/
  Plugin.php    CurrencyRatesProviderInterface implementation
  Client.php    CBR XML API client
  Parser.php    XML_daily.asp response parser
tests/
  data/         sample CBR responses
```

## Development

```
composer install
./vendor/bin/phpunit
```
