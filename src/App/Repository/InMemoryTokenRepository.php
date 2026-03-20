<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Token;
use App\Entity\TokenCollection;
use App\Enum\Permission;
use App\Provider\TokenDataProvider;

class InMemoryTokenRepository implements TokenRepositoryInterface
{
    private TokenCollection $tokens;

    public function __construct(TokenDataProvider $tokenDataProvider)
    {
        $this->tokens = $this->mapToCollection($tokenDataProvider->getTokens());
    }

    public function findById(string $id): ?Token
    {
        return $this->tokens->findById($id);
    }

    public function findAll(): TokenCollection
    {
        return $this->tokens;
    }

    /**
     * @param array<array{token: string, permissions: string[]}> $rawTokens
     */
    private function mapToCollection(array $rawTokens): TokenCollection
    {
        $tokens = array_map(
            fn(array $data) => new Token(
                $data['token'],
                $this->mapPermissions($data['permissions'])
            ),
            $rawTokens
        );

        return new TokenCollection($tokens);
    }

    /**
     * @param string[] $permissions
     * @return Permission[]
     */
    private function mapPermissions(array $permissions): array
    {
        return array_values(array_filter(
            array_map(
                fn(string $value) => Permission::tryFrom($value),
                $permissions
            )
        ));
    }
}
