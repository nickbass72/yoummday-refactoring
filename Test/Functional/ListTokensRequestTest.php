<?php

declare(strict_types=1);

namespace Test\Functional;

use GuzzleHttp\Exception\GuzzleException;
use Test\Base\FunctionalTestCase;

class ListTokensRequestTest extends FunctionalTestCase
{
    /**
     * @param string $url
     * @param int $expectedStatusCode
     * @param array $expectedData
     * @return void
     * @throws GuzzleException
     *
     * @dataProvider availableRequestsProvider
     */
    public function testAvailableRequests(string $url, int $expectedStatusCode, array $expectedData): void
    {
        $response = $this->httpClient->get($url);
        $data = $this->getResponseData($response);

        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertEquals($expectedData, $data);
    }

    public static function availableRequestsProvider(): array
    {
        return [
            'list tokens' => [
                'url' => self::buildUrl('/tokens'),
                'expectedStatusCode' => 200,
                'expectedData' => [
                    [
                        'token' => 'token1234',
                        'permissions' => ['read', 'write'],
                    ],
                    [
                        'token' => 'tokenReadonly',
                        'permissions' => ['read'],
                    ],
                ],
            ],
        ];
    }
}
