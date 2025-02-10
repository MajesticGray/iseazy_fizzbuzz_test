<?php
declare(strict_types=1);

namespace App\Application\Dto;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This data transfer class contains the output data sent by the
 *   API together with the response
 */
class FizzBuzzOutputDto
{
    public function __construct(
        #[Groups(['fizzbuzz:read'])]
        public int $start,
        #[Groups(['fizzbuzz:read'])]
        public int $end,
        #[Groups(['fizzbuzz:read'])]
        public string $fizzBuzz,
        #[Groups(['fizzbuzz:read'])]
        public DateTimeInterface $createdAt,
    ) {
    }
}
