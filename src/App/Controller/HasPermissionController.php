<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Permission;
use App\Exception\TokenProviderException;
use App\Provider\TokenProviderInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

#[Route(httpMethod: HttpMethod::GET, uri: '/tokens/{token}/has-{permission}-permission')]
class HasPermissionController implements HandlerInterface
{
    public function __construct(
        private readonly TokenProviderInterface $tokenProvider
    ) {
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $validationResult = $this->validate($parameters);
        if ($validationResult instanceof JSONResponse) {
            return $validationResult;
        }

        $tokenParam = $parameters->get('token');
        $permissionParam = $parameters->get('permission');

        try {
            $token = $this->tokenProvider->get($tokenParam);
        } catch (TokenProviderException $exception) {
            return new JSONResponse(['error' => sprintf('Token "%s" not found', $tokenParam)], 404);
        }

        return new JSONResponse([
            'permission' => $token->hasPermission(Permission::from($permissionParam)),
        ], 200);
    }

    private function validate(RouteParameters $parameters): ?JSONResponse
    {
        $tokenParam = $parameters->get('token');
        $permissionParam = $parameters->get('permission');

        if ($tokenParam === null) {
            return new JSONResponse(['error' => 'Token not found'],404);
        }

        if ($permissionParam === null || Permission::tryFrom($permissionParam) === null) {
            return new JSONResponse(
                ['error' => sprintf('Invalid permission parameter "%s"', $permissionParam)],
                400
            );
        }

        return null;
    }
}
