<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Infrastructure\Doctrine\Entity\FizzBuzzRun;
use Override;

/**
 * This repository fetches fizzbuzz runs from the database
 */
class FizzBuzzRunRepository extends AbstractRepository
{
    #[Override]
    protected function getEntityClass(): string
    {
        return FizzBuzzRun::class;
    }

    public function save(FizzBuzzRun $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
