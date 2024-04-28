<?php

namespace Test\Unit\Controller;

use App\Controller\ShowTokenController;
use App\Entity\Token;
use App\Enum\Permission;
use App\Exception\TokenProviderException;
use App\Provider\TokenProviderInterface;
use App\Serializer\TokenSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ShowTokenControllerTest extends TestCase
{
    /**
     * @var TokenProviderInterface&MockObject
     */
    private TokenProviderInterface $tokenProvider;

    /**
     * @var TokenSerializer&MockObject
     */
    private TokenSerializer $tokenSerializer;

    /**
     * @var RequestInterface&MockObject
     */
    private RequestInterface $request;

    private ShowTokenController $showTokenController;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenProvider = $this->createMock(TokenProviderInterface::class);
        $this->tokenSerializer = $this->createMock(TokenSerializer::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->showTokenController = new ShowTokenController($this->tokenProvider, $this->tokenSerializer);
    }

    /**
     * @param Token|Throwable $providerResult
     * @param RouteParameters $routeParameters
     * @param array $normalizedToken
     * @param ResponseInterface $expectedResponse
     * @return void
     *
     * @dataProvider invokeProvider
     */
    public function testInvoke(
        Token|Throwable $providerResult,
        RouteParameters $routeParameters,
        array $normalizedToken,
        ResponseInterface $expectedResponse
    ): void {
        if ($providerResult instanceof Throwable) {
            $this->tokenProvider
                ->method('get')
                ->willThrowException($providerResult)
            ;
        } else {
            $this->tokenProvider
                ->method('get')
                ->willReturn($providerResult)
            ;

            $this->tokenSerializer
                ->method('normalize')
                ->willReturn($normalizedToken)
            ;
        }

        $response = $this->showTokenController->__invoke($this->request, $routeParameters);

        $this->assertEquals($expectedResponse, $response);
    }

    public static function invokeProvider(): array
    {
        $normalizedToken = [
            'token' => '1234',
            'permissions' => [Permission::READ->value, Permission::WRITE->value]
        ];

        return [
            'token found' => [
                'providerResult' => new Token('1234', [Permission::READ, Permission::WRITE]),
                'routeParameters' => new RouteParameters(['token' => '1234']),
                'normalizedToken' => $normalizedToken,
                'expectedResponse' => new JSONResponse($normalizedToken, 200),
            ],
            'token not found' => [
                'providerResult' => new TokenProviderException(),
                'routeParameters' => new RouteParameters(['token' => '1234']),
                'normalizedToken' => [],
                'expectedResponse' => new JSONResponse(
                    ['error' => 'Token "1234" not found'],
                    404
                ),
            ],
        ];
    }
}