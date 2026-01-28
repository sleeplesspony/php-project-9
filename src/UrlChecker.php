<?php

namespace Hexlet\Code;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class UrlChecker
{
    public static function getUrlCheckData(string $url): array
    {
        $client = new Client(['timeout'  => 2.0]);
        $checkResponse = $client->request('GET', $url);
        $content = optional($checkResponse->getBody())->getContents();

        $crawler = new Crawler($content);
        $h1 = optional($crawler->filter('h1')->getNode(0))->textContent;
        $title = optional($crawler->filter('title')->getNode(0))->textContent;
        $description = optional($crawler->filter('meta[name="description"]')->getNode(0))->getAttribute('content');

        return [
            "code" => $checkResponse->getStatusCode(),
            "h1" => $h1,
            "title" => $title,
            "description" => $description
        ];

    }
}