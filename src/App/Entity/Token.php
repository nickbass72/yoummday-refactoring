<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Permission;

class Token
{
    readonly private string $token;

    private array $permissions = [];

    /**
     * @param string $token
     * @param Permission[] $permissions
     */
    public function __construct(string $token, array $permissions = [])
    {
        $this->token = $token;

        foreach ($permissions as $permission) {
            $this->addPermission($permission);
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!in_array($permission, $this->permissions, true)) {
            $this->permissions[] = $permission;
        }

        return $this;
    }

    public function hasPermission(Permission $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function removePermission(Permission $permission): self
    {
        $key = array_search($permission, $this->permissions, true);

        if ($key !== false) {
            unset($this->permissions[$key]);
        }

        return $this;
    }
}
