<?php

namespace Test\Unit\Controller;

use App\Controller\ListTokensController;
use App\Entity\Token;
use App\Enum\Permission;
use App\Provider\TokenProviderInterface;
use App\Serializer\TokenSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class ListTokensControllerTest extends TestCase
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

    private ListTokensController $listTokensController;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenProvider = $this->createMock(TokenProviderInterface::class);
        $this->tokenSerializer = $this->createMock(TokenSerializer::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->listTokensController = new ListTokensController($this->tokenProvider, $this->tokenSerializer);
    }

    public function testInvoke(): void
    {
        $token = new Token('1234', [Permission::READ, Permission::WRITE]);

        $normalizedToken = [
            'token' => '1234',
            'permissions' => [Permission::READ->value, Permission::WRITE->value]
        ];

        $this->tokenProvider
            ->method('all')
            ->willReturn([$token])
        ;

        $this->tokenSerializer
            ->method('normalize')
            ->willReturn($normalizedToken)
        ;

        $response = $this->listTokensController->__invoke($this->request, new RouteParameters([]));
        $expectedResponse = new JSONResponse([$normalizedToken], 200);

        $this->assertEquals($expectedResponse, $response);
    }
}