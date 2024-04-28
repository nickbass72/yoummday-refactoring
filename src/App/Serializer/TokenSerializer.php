<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Token;
use App\Enum\Permission;

class TokenSerializer
{
    public function normalize(Token $data): array
    {
        $result = [
            'token' => $data->getToken(),
            'permissions' => []
        ];

        foreach ($data->getPermissions() as $token) {
            $result['permissions'][] = $token->value;
        }

        return $result;
    }

    public function denormalize(array $data): Token
    {
        $token = new Token($data['token'] ?? '');

        foreach ($data['permissions'] ?? [] as $permission) {
            $token->addPermission(Permission::from($permission));
        }

        return $token;
    }
}
