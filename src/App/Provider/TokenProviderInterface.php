<?php

declare(strict_types=1);

namespace App\Provider;

use App\Entity\Token;

interface TokenProviderInterface
{
    public function all(): array;

    public function get(string $tokenId): Token;
}