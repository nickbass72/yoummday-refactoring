<?php

declare(strict_types=1);

namespace Test\Functional;

use GuzzleHttp\Exception\GuzzleException;
use Test\Base\FunctionalTestCase;

class ShowTokenRequestTest extends FunctionalTestCase
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
            'show token' => [
                'url' => self::buildUrl('/tokens/token1234'),
                'expectedStatusCode' => 200,
                'expectedData' => [
                    'token' => 'token1234',
                    'permissions' => ['read', 'write'],
                ],
            ],
            'token not found' => [
                'url' => self::buildUrl('/tokens/INVALID_TOKEN'),
                'expectedStatusCode' => 404,
                'expectedData' => [
                    'error' => 'Token "INVALID_TOKEN" not found',
                ],
            ],
        ];
    }
}
