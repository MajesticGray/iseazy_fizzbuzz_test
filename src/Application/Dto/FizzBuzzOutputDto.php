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
        #[Groups(['fizzbuz:read'])]
        public int $start,
        #[Groups(['fizzbuz:read'])]
        public int $end,
        #[Groups(['fizzbuz:read'])]
        public string $fizzBuzz,
        #[Groups(['fizzbuz:read'])]
        public DateTimeInterface $createdAt,
    ) {
    }
}
