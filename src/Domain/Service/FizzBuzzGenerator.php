<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Exception\FizzBuzzException;
use App\Domain\Exception\FizzBuzzOutOfRangeException;
use Generator;

/**
 * The FizzBuzz sequence generator service
 */
class FizzBuzzGenerator
{
    public function __construct(
        // fizzbuzz.max_range will be injected from config
        private int $maxRange,
    ) {
    }

    /**
     * Generates the FizzBuzz sequence for a given range.
     *
     * @param int $start The starting number of the sequence.
     * @param int $end The ending number of the sequence.
     * @return Generator Yields each computed FizzBuzz value lazily.
     * @throws FizzBuzzException|FizzBuzzOutOfRangeException If the provided range is invalid
     */
    public function generate(int $start, int $end): Generator
    {
        $this->validate(start: $start, end: $end);
        for ($i = $start; $i <= $end; $i++) {
            yield $this->generateForNumber($i);
        }
    }

    /**
     * Computes the FizzBuzz value for a single number.
     *
     * @param int $number The number to compute FizzBuzz for.
     * @return string The FizzBuzz value ("Fizz", "Buzz", "FizzBuzz", or the number itself).
     */
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

    /**
     * Validates the input range to ensure proper constraints.
     *
     * @param int $start The starting number.
     * @param int $end The ending number.
     * @throws FizzBuzzException|FizzBuzzOutOfRangeException If constraints are violated.
     */
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
}
