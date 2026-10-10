<?php
namespace OhMyShares\PluginCbrCurrencyRates;

use DateTimeInterface;
use OhMyShares\Plugin\BasePlugin;
use OhMyShares\Plugin\CurrencyRatesProviderInterface;
use OhMyShares\Plugin\Dtos\CurrencyRate;

class Plugin extends BasePlugin implements CurrencyRatesProviderInterface
{
    const VERSION = '1.0.0';

    public function __construct(private ?Client $client = null)
    {
    }

    public function title(): string
    {
        return 'CBR currency rates';
    }

    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * @return CurrencyRate[]
     */
    public function currencyRates(DateTimeInterface $date): array
    {
        return (new Parser)->parse($this->client()->daily($date));
    }

    private function client(): Client
    {
        return $this->client ??= new Client('OhMyShares-CBR-Plugin/'.$this->version());
    }
}
