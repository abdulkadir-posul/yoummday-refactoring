<?php

declare(strict_types=1);

namespace App\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Token>
 */
class TokenCollection implements IteratorAggregate, Countable
{
    /**
     * @param Token[] $tokens
     */
    public function __construct(
        private readonly array $tokens
    ) {
    }

    public function findById(string $id): ?Token
    {
        foreach ($this->tokens as $token) {
            if ($token->getId() === $id) {
                return $token;
            }
        }

        return null;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tokens);
    }

    public function count(): int
    {
        return count($this->tokens);
    }
}
