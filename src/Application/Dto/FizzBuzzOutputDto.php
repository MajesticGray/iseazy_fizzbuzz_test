<?php
declare(strict_types=1);

namespace App\Application\Dto;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This data transfer object contains the output data for the request response
 * This is the object representation used by the API
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
