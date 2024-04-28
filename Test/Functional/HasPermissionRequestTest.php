<?php

declare(strict_types=1);

namespace Test\Functional;

use GuzzleHttp\Exception\GuzzleException;
use Test\Base\FunctionalTestCase;

class HasPermissionRequestTest extends FunctionalTestCase
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
            'token has read permission' => [
                'url' => self::buildUrl('/tokens/token1234/has-read-permission'),
                'expectedStatusCode' => 200,
                'expectedData' => [
                    'permission' => true,
                ],
            ],
            'token has no write permission' => [
                'url' => self::buildUrl('/tokens/tokenReadonly/has-write-permission'),
                'expectedStatusCode' => 200,
                'expectedData' => [
                    'permission' => false,
                ],
            ],
            'token not found' => [
                'url' => self::buildUrl('/tokens/INVALID_TOKEN/has-read-permission'),
                'expectedStatusCode' => 404,
                'expectedData' => [
                    'error' => 'Token "INVALID_TOKEN" not found',
                ],
            ],
            'token found but invalid permission parameter' => [
                'url' => self::buildUrl('/tokens/token1234/has-INVALID-permission'),
                'expectedStatusCode' => 400,
                'expectedData' => [
                    'error' => 'Invalid permission parameter "INVALID"',
                ],
            ],
        ];
    }
}
