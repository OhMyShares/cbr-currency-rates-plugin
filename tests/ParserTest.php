<?php
namespace OhMyShares\PluginCbrCurrencyRates\Tests;

use OhMyShares\Plugin\Dtos\CurrencyRate;
use OhMyShares\PluginCbrCurrencyRates\Parser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ParserTest extends TestCase
{
    const DAILY = __DIR__.'/data/XML_daily.xml';

    public function testParsesDailyRates(): void
    {
        $rates = (new Parser)->parse(file_get_contents(self::DAILY));
        $byCode = [];
        foreach ($rates as $rate) {
            $this->assertInstanceOf(CurrencyRate::class, $rate);
            $this->assertSame('RUB', $rate->base);
            $this->assertSame('2026-10-03', $rate->date);
            $byCode[$rate->currency] = $rate->rate;
        }
        $this->assertSame(['USD', 'EUR', 'KZT', 'JPY', 'XDR'], array_keys($byCode));
        $this->assertEqualsWithDelta(81.5432, $byCode['USD'], 1e-9);
        $this->assertEqualsWithDelta(0.162345, $byCode['KZT'], 1e-9);
        $this->assertEqualsWithDelta(0.54321, $byCode['JPY'], 1e-9);
    }

    public function testRejectsUnexpectedResponse(): void
    {
        $this->expectException(RuntimeException::class);
        (new Parser)->parse('Error in parameters');
    }

    public function testRejectsInvalidDate(): void
    {
        $this->expectException(RuntimeException::class);
        (new Parser)->parse('<?xml version="1.0"?><ValCurs Date="2026-10-03"></ValCurs>');
    }
}
