<?php

declare(strict_types=1);

namespace Test\Unit\Serializer;

use App\Entity\Token;
use App\Enum\Permission;
use App\Serializer\TokenSerializer;
use PHPUnit\Framework\TestCase;

class TokenSerializerTest extends TestCase
{
    private TokenSerializer $tokenSerializer;

    protected function setUp(): void
    {
        $this->tokenSerializer = new TokenSerializer();
    }

    /**
     * @param Token $token
     * @param array $expectedResult
     * @return void
     *
     * @dataProvider normalizeProvider
     */
    public function testNormalize(Token $token, array $expectedResult): void
    {
        $result = $this->tokenSerializer->normalize($token);
        $this->assertEquals($expectedResult, $result);
    }

    public static function normalizeProvider(): array
    {
        return [
            'test with included permissions' => [
                'token' => new Token('1234', [Permission::READ, Permission::WRITE]),
                'expectedResult' => [
                    'token' => '1234',
                    'permissions' => [Permission::READ->value, Permission::WRITE->value],
                ],
            ],
            'test without included permissions' => [
                'token' => new Token('1234'),
                'expectedResult' => [
                    'token' => '1234',
                    'permissions' => [],
                ],
            ],
        ];
    }

    /**
     * @param array $data
     * @param Token $expectedResult
     * @return void
     *
     * @dataProvider denormalizeProvider
     */
    public function testDenormalize(array $data, Token $expectedResult): void
    {
        $result = $this->tokenSerializer->denormalize($data);
        $this->assertEquals($expectedResult, $result);
    }

    public static function denormalizeProvider(): array
    {
        return [
            'test with included permissions' => [
                'data' => [
                    'token' => '1234',
                    'permissions' => [Permission::READ->value, Permission::WRITE->value],
                ],
                'expectedResult' => new Token('1234', [Permission::READ, Permission::WRITE]),
            ],
            'test without included permissions' => [
                'data' => [
                    'token' => '1234',
                ],
                'expectedResult' => new Token('1234'),
            ],
        ];
    }
}
