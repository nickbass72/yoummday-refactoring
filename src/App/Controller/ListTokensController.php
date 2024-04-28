<?php

declare(strict_types=1);

namespace App\Controller;

use App\Provider\TokenProviderInterface;
use App\Serializer\TokenSerializer;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

#[Route(httpMethod: HttpMethod::GET, uri: '/tokens')]
class ListTokensController implements HandlerInterface
{
    public function __construct(
        private readonly TokenProviderInterface $tokenProvider,
        private readonly TokenSerializer $tokenSerializer
    ) {
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $tokenList = [];

        foreach ($this->tokenProvider->all() as $token) {
            $tokenList[] = $this->tokenSerializer->normalize($token);
        }

        return new JSONResponse($tokenList, 200);
    }
}
