<?php
declare(strict_types=1);

namespace App\Tests\Unit\Domain;

use App\Domain\Exception\FizzBuzzException;
use App\Domain\Exception\FizzBuzzOutOfRangeException;
use App\Domain\Service\FizzBuzzGenerator;
use PHPUnit\Framework\TestCase;

/**
 * This test validates that the FizzBuzz generation sequence and error handling.
 */
class FizzBuzzGeneratorTest extends TestCase
{
    /**
     * Ensure that the generation is working as expected, in a range from 30 to 67.
     * @group unit
     * @group domain
     * @return void
     */
    public function testFizzBuzzGenerationReturnsValidSequence(): void
    {
        $expectedResults = [
            'FizzBuzz', '31', '32', 'Fizz', '34', 'Buzz', 'Fizz', '37', '38', 'Fizz', 'Buzz',
            '41', 'Fizz', '43', '44', 'FizzBuzz', '46', '47', 'Fizz', '49', 'Buzz', 'Fizz',
            '52', '53', 'Fizz', 'Buzz', '56', 'Fizz', '58', '59', 'FizzBuzz', '61', '62',
            'Fizz', '64', 'Buzz', 'Fizz', '67',
        ];

        $fizzBuzzGenerator = new FizzBuzzGenerator(maxRange: 50);
        $generatedResults  = iterator_to_array($fizzBuzzGenerator->generate(30, 67));

        $this->assertSame($expectedResults, $generatedResults);
    }

    /**
     * Ensure that a FizzBuzzOutOfRangeException is thrown when trying to generate
     *   more than FIZZBUZZ_MAX_RANGE. This variable is configurable in the .env file.
     * @group unit
     * @group domain
     * @return void
     */
    public function testFizzBuzzGenerationThrowsOutOfRangeException(): void
    {
        $this->expectException(FizzBuzzOutOfRangeException::class);
        $fizzBuzzGenerator = new FizzBuzzGenerator(10);
        $sequence          = $fizzBuzzGenerator->generate(1, 1203);
        // trigger the actual generation
        iterator_to_array($sequence);
    }

    /**
     * Ensure that a FizzBuzzException is thrown when the end number is smaller
     *   than the start number.
     * @group unit
     * @group domain
     * @return void
     */
    public function testFizzBuzzGenerationEnsuresRangeCoherence(): void
    {
        $this->expectException(FizzBuzzException::class);
        $fizzBuzzGenerator = new FizzBuzzGenerator(10);
        $sequence          = $fizzBuzzGenerator->generate(10, 1);
        // trigger the actual generation
        iterator_to_array($sequence);
    }

    /**
     * Ensure that a FizzBuzzException is thrown when the start or end numbers are
     *   not Natural numbers (> 0)
     * @group unit
     * @group domain
     * @return void
     */
    public function testFizzBuzzGenerationAcceptsNaturalNumbers(): void
    {
        $this->expectException(FizzBuzzException::class);
        $fizzBuzzGenerator = new FizzBuzzGenerator(10);
        $sequence          = $fizzBuzzGenerator->generate(0, 3);
        // trigger the actual generation
        iterator_to_array($sequence);
    }
}
