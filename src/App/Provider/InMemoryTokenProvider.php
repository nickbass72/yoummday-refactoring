<?php

declare(strict_types=1);

namespace App\Provider;

use App\Entity\Token;
use App\Exception\TokenProviderException;
use App\Serializer\TokenSerializer;
use Progphil1337\Config\Config;

class InMemoryTokenProvider implements TokenProviderInterface
{
    /**
     * @var Token[]
     */
    private array $tokens;

    public function __construct(
        private readonly Config $config,
        private readonly TokenSerializer $tokenSerializer
    ) {
        $this->tokens = [];

        foreach ($this->config->get('tokens') as $item) {
            $token = $this->tokenSerializer->denormalize($item);
            $this->tokens[$token->getToken()] = $token;
        }
    }

    public function all(): array
    {
        return $this->tokens;
    }

    public function get(string $tokenId): Token
    {
        if (array_key_exists($tokenId, $this->tokens)) {
            return $this->tokens[$tokenId];
        }

        throw new TokenProviderException(sprintf('Token "%s" not found', $tokenId));
    }
}
