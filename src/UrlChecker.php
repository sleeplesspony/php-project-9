<?php

namespace Hexlet\Code;
use GuzzleHttp\Client;

class UrlChecker
{
    public static function getUrlCheckData(string $url): array
    {
        $client = new Client(['timeout'  => 2.0]);
        $checkResponse = $client->request('GET', $url);

        return [
            "code" => $checkResponse->getStatusCode()
        ];

    }
}