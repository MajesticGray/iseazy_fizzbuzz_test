<?php
declare(strict_types=1);

namespace App\Domain\Model;

use DateTimeInterface;

/**
 * Invariable domain structure that holds the details about a FizzBuzz generation
 */
class FizzBuzz
{
    public function __construct(
        public int $start,
        public int $end,
        public string $fizzBuzz,
        public DateTimeInterface $createdAt,
    ) {
    }
}
