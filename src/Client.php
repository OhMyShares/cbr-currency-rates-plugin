<?php
namespace OhMyShares\PluginCbrCurrencyRates;

use DateTimeInterface;
use RuntimeException;

/**
 * Central Bank of Russia XML API client
 */
class Client
{
    const URL_DAILY = 'https://www.cbr.ru/scripts/XML_daily.asp';

    public function __construct(private readonly string $userAgent, private readonly int $timeout = 30)
    {
    }

    /**
     * Daily rates XML effective on the given date
     */
    public function daily(DateTimeInterface $date): string
    {
        return $this->request(self::URL_DAILY.'?'.http_build_query(['date_req' => $date->format('d/m/Y')]));
    }

    private function request(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => $this->userAgent,
        ]);
        $body = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if (!is_string($body) || $error !== '') {
            throw new RuntimeException('CBR request '.$url.' failed: '.$error);
        }
        if ($status !== 200) {
            throw new RuntimeException('CBR request '.$url.' failed: HTTP '.$status);
        }
        return $body;
    }
}
