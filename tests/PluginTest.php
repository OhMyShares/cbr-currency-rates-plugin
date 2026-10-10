<?php
namespace OhMyShares\PluginCbrCurrencyRates\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use OhMyShares\Plugin\CurrencyRatesProviderInterface;
use OhMyShares\PluginCbrCurrencyRates\Client;
use OhMyShares\PluginCbrCurrencyRates\Plugin;
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase
{
    public function testProvidesRatesForRequestedDate(): void
    {
        $client = new class('test') extends Client {
            public ?string $requested = null;

            public function daily(DateTimeInterface $date): string
            {
                $this->requested = $date->format('Y-m-d');
                return file_get_contents(ParserTest::DAILY);
            }
        };
        $plugin = new Plugin($client);

        $this->assertInstanceOf(CurrencyRatesProviderInterface::class, $plugin);
        $rates = $plugin->currencyRates(new DateTimeImmutable('2026-10-04'));
        $this->assertSame('2026-10-04', $client->requested);
        $this->assertCount(5, $rates);
        $this->assertSame('2026-10-03', $rates[0]->date);
    }

    public function testMetadata(): void
    {
        $manifest = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
        $plugin = new Plugin;
        $this->assertSame('CBR currency rates', $plugin->title());
        $this->assertSame($manifest['version'], $plugin->version());
        $this->assertSame(Plugin::class, $manifest['extra']['ohmyshares']['class']);
    }
}
