<?php

declare(strict_types=1);

namespace Test\Unit\Provider;

use App\Entity\Token;
use App\Enum\Permission;
use App\Exception\TokenProviderException;
use App\Provider\InMemoryTokenProvider;
use App\Serializer\TokenSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Progphil1337\Config\Config;

class InMemoryTokenProviderTest extends TestCase
{
    private InMemoryTokenProvider $inMemoryTokenProvider;

    private Token $availableToken;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $configuredTokens = [
            ['token' => 'token1234', 'permissions' => ['read', 'write']]
        ];

        $config = $this->createMock(Config::class);
        $config
            ->method('get')
            ->willReturn($configuredTokens)
        ;

        $this->availableToken = new Token('token1234', [Permission::READ, Permission::WRITE]);

        $tokenSerializer = $this->createMock(TokenSerializer::class);
        $tokenSerializer
            ->method('denormalize')
            ->willReturn($this->availableToken)
        ;

        $this->inMemoryTokenProvider = new InMemoryTokenProvider($config, $tokenSerializer);
    }

    public function testAll(): void
    {
        $this->assertSame(
            [$this->availableToken->getToken() => $this->availableToken],
            $this->inMemoryTokenProvider->all()
        );
    }

    /**
     * @throws TokenProviderException
     */
    public function testGetSuccess(): void
    {
        $this->assertSame($this->availableToken, $this->inMemoryTokenProvider->get('token1234'));
    }

    /**
     * @throws TokenProviderException
     */
    public function testGetError(): void
    {
        $this->expectException(TokenProviderException::class);
        $this->inMemoryTokenProvider->get('INVALID_TOKEN');
    }
}
