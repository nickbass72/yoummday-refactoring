<?php

declare(strict_types=1);

namespace App\Provider;

use App\Entity\Token;
use App\Exception\TokenProviderException;

interface TokenProviderInterface
{
    public function all(): array;

    /**
     * @param string $tokenId
     * @return Token
     * @throws TokenProviderException
     */
    public function get(string $tokenId): Token;
}