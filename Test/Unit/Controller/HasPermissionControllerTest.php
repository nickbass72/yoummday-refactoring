<?php

namespace Test\Unit\Controller;

use App\Controller\HasPermissionController;
use App\Entity\Token;
use App\Enum\Permission;
use App\Exception\TokenProviderException;
use App\Provider\TokenProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class HasPermissionControllerTest extends TestCase
{
    /**
     * @var TokenProviderInterface&MockObject
     */
    private TokenProviderInterface $tokenProvider;

    /**
     * @var RequestInterface&MockObject
     */
    private RequestInterface $request;

    private HasPermissionController $hasPermissionController;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenProvider = $this->createMock(TokenProviderInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->hasPermissionController = new HasPermissionController($this->tokenProvider);
    }

    /**
     * @param Token|Throwable $providerResult
     * @param RouteParameters $routeParameters
     * @param ResponseInterface $expectedResponse
     * @return void
     *
     * @dataProvider invokeProvider
     */
    public function testInvoke(
        Token|Throwable $providerResult,
        RouteParameters $routeParameters,
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
        }

        $response = $this->hasPermissionController->__invoke($this->request, $routeParameters);

        $this->assertEquals($expectedResponse, $response);
    }

    public static function invokeProvider(): array
    {
        return [
            'token found and read permission = true' => [
                'providerResult' => new Token('1234', [Permission::READ]),
                'routeParameters' => new RouteParameters([
                    'token' => '1234',
                    'permission' => Permission::READ->value,
                ]),
                'expectedResponse' => new JSONResponse(['permission' => true], 200),
            ],
            'token found and write permission = false' => [
                'providerResult' => new Token('1234', [Permission::READ]),
                'routeParameters' => new RouteParameters([
                    'token' => '1234',
                    'permission' => Permission::WRITE->value,
                ]),
                'expectedResponse' => new JSONResponse(['permission' => false], 200),
            ],
            'token found but invalid permission value passed' => [
                'providerResult' => new Token('1234', [Permission::READ]),
                'routeParameters' => new RouteParameters([
                    'token' => '1234',
                    'permission' => 'INVALID_PERMISSION_VALUE',
                ]),
                'expectedResponse' => new JSONResponse(
                    ['error' => 'Invalid permission parameter "INVALID_PERMISSION_VALUE"'],
                    400
                ),
            ],
            'token not found' => [
                'providerResult' => new TokenProviderException(),
                'routeParameters' => new RouteParameters([
                    'token' => '1234',
                    'permission' => Permission::READ->value,
                ]),
                'expectedResponse' => new JSONResponse(
                    ['error' => 'Token "1234" not found'],
                    404
                ),
            ],
        ];
    }
}