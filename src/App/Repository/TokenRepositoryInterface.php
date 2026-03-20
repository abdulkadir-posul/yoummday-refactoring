<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Token;
use App\Entity\TokenCollection;

interface TokenRepositoryInterface
{
    public function findById(string $id): ?Token;

    public function findAll(): TokenCollection;
}
