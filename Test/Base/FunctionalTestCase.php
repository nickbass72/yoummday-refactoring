<?php

namespace Test\Base;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Progphil1337\Config\Config;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Process\Process;

abstract class FunctionalTestCase extends TestCase
{
    private static string $baseUrl;

    private static Process $process;

    protected Client $httpClient;

    public static function setUpBeforeClass(): void
    {
        $rootPath = dirname(__DIR__, 2);
        $commandPath = realpath($rootPath . '/bin/serve');

        self::$process = new Process([$commandPath]);
        self::$process->start();

        usleep(100000);
    }

    public static function tearDownAfterClass(): void
    {
        self::$process->stop();
    }

    protected static function buildUrl(string $uri): string
    {
        if (!isset(self::$baseUrl)) {
            $rootPath = dirname(__DIR__, 2);
            $config = Config::create([$rootPath . '/config/app.json']);
            self::$baseUrl = sprintf('http://%s:%s',$config->get('host'), $config->get('port'));
        }

        return self::$baseUrl . '/' . ltrim($uri, '/');
    }

    protected function setUp(): void
    {
        $this->httpClient = new Client(['http_errors' => false]);
    }

    protected function getResponseData(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
