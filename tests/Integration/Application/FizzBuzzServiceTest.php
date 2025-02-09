<?php
declare(strict_types=1);

namespace App\Tests\Integration\Domain;

use App\Domain\Service\FizzBuzzGenerator;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * This tests if the service works inside the Symfony container.
 */
class FizzBuzzServiceTest extends KernelTestCase
{
    /**
     * Ensures the FizzBuzzGenerator service is injected correctly
     * @group application
     * @group integration
     * @return void
     */
    public function testFizzBuzzGeneratorIsWiredCorrectly(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var FizzBuzzGenerator $fizzBuzzGenerator */
        $fizzBuzzGenerator = $container->get(FizzBuzzGenerator::class);

        $this->assertInstanceOf(FizzBuzzGenerator::class, $fizzBuzzGenerator);
        $this->assertSame(100, $container->getParameter('fizzbuzz.max_range'));
    }

    /**
     * Ensures the service returns a sequence generator.
     * @group application
     * @group integration
     * @return void
     */
    public function testFizzBuzzServiceReturnsSequenceGenerator(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var FizzBuzzGenerator $fizzBuzzGenerator */
        $fizzBuzzGenerator = $container->get(FizzBuzzGenerator::class);
        $sequenceGenerator = $fizzBuzzGenerator->generate(start: 1, end: 15);

        $this->assertInstanceOf(expected: Generator::class, actual: $sequenceGenerator);
    }
}
