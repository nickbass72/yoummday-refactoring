<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\TokenProviderException;
use App\Provider\TokenProviderInterface;
use App\Serializer\TokenSerializer;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

#[Route(httpMethod: HttpMethod::GET, uri: '/tokens/{token}')]
class ShowTokenController implements HandlerInterface
{
    public function __construct(
        private readonly TokenProviderInterface $tokenProvider,
        private readonly TokenSerializer $tokenSerializer
    ) {
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $validationResult = $this->validate($parameters);
        if ($validationResult instanceof JSONResponse) {
            return $validationResult;
        }

        $tokenParam = $parameters->get('token');

        try {
            $token = $this->tokenProvider->get($tokenParam);
        } catch (TokenProviderException $exception) {
            return new JSONResponse(['error' => $exception->getMessage()], 404);
        }

        return new JSONResponse($this->tokenSerializer->normalize($token), 200);
    }

    private function validate(RouteParameters $parameters): ?JSONResponse
    {
        $tokenParam = $parameters->get('token');

        if ($tokenParam === null) {
            return new JSONResponse(['error' => 'Not found'], 404);
        }

        return null;
    }
}
