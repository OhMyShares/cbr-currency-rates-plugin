<?php
namespace OhMyShares\PluginCbrCurrencyRates;

use DateTimeImmutable;
use OhMyShares\Plugin\Dtos\CurrencyRate;
use RuntimeException;

/**
 * Converts CBR daily rates XML into SDK currency rates
 */
class Parser
{
    const BASE = 'RUB';

    /**
     * @return CurrencyRate[]
     */
    public function parse(string $xml): array
    {
        $useErrors = libxml_use_internal_errors(true);
        try {
            $doc = simplexml_load_string($xml);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($useErrors);
        }
        if ($doc === false || $doc->getName() !== 'ValCurs') {
            throw new RuntimeException('Unexpected CBR response: '.substr(trim($xml), 0, 200));
        }
        $date = DateTimeImmutable::createFromFormat('!d.m.Y', (string)$doc['Date']);
        if (!$date) {
            throw new RuntimeException('Unexpected CBR rates date "'.$doc['Date'].'"');
        }

        $rates = [];
        foreach ($doc->Valute as $valute) {
            $code = strtoupper(trim((string)$valute->CharCode));
            $nominal = (int)$valute->Nominal;
            $value = (float)str_replace([',', ' '], ['.', ''], trim((string)$valute->Value));
            if ($code === '' || $nominal <= 0 || $value <= 0) {
                continue;
            }
            $rates[] = new CurrencyRate($date->format('Y-m-d'), self::BASE, $code, $value / $nominal);
        }
        return $rates;
    }
}
