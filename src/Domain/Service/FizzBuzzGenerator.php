<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Exception\FizzBuzzException;
use App\Domain\Exception\FizzBuzzOutOfRangeException;
use Generator;

class FizzBuzzGenerator
{
    public function __construct(
        private int $maxRange,
    ) {
    }

    public function generate(int $start, int $end): Generator
    {
        $this->validate(start: $start, end: $end);
        for ($i = $start; $i <= $end; $i++) {
            yield $this->generateForNumber($i);
        }
    }

    private function validate(int $start, int $end): void
    {
        if ($start > $end) {
            throw new FizzBuzzException(sprintf('Invalid range: %d to %d. The start number must be smaller.', $start, $end));
        }
        if ($start <= 0 || $end <= 0) {
            throw new FizzBuzzException('Start and end numbers must be Natural numbers.');
        }
        if ($end - $start >= $this->maxRange) {
            throw new FizzBuzzOutOfRangeException("You can't generate more than {$this->maxRange} numbers.");
        }
    }

    protected function generateForNumber(int $number): string
    {
        $output = '';
        if ($number % 3 === 0) {
            $output .= 'Fizz';
        }
        if ($number % 5 === 0) {
            $output .= 'Buzz';
        }

        return $output ?: (string)$number;

        // one-liner alternative
        // IMHO not worth it;
        //    a few lines of code increase enormously the readability
        //    without adding performance issues.
        //return ((['Fizz'][$number % 3] ?? '') . (['Buzz'][$number % 5] ?? '')) ?: (string)$number;
    }
}
