<?php
declare(strict_types=1);

namespace App\Tests\Integration\Persistence;

use App\Infrastructure\Persistence\Repository\FizzBuzzRunRepository;
use App\Infrastructure\Doctrine\Entity\FizzBuzzRun;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FizzBuzzRunRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private FizzBuzzRunRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container           = self::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repository    = $this->entityManager->getRepository(FizzBuzzRun::class);
    }

    /**
     * Ensures that FizzBuzzRun entities are well persisted into the database
     * @group persistence
     * @group integration
     * @return void
     */
    public function testFizzBuzzRunIsPersistedCorrectly(): void
    {
        $fizzBuzzRun = new FizzBuzzRun(
            initialNumber: 1,
            finalNumber: 15,
            fizzBuzz: "1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz",
        );

        // Persist and flush to the database
        $this->entityManager->persist($fizzBuzzRun);
        $this->entityManager->flush();

        // Check if the entity was persisted correctly
        /** @var ?FizzBuzzRun */
        $fizzBuzzRun = $this->repository->findOneBy([]);

        $this->assertNotNull(actual: $fizzBuzzRun, message: "The FizzBuzzRun entity should be persisted.");
        $this->assertSame(expected: 1, actual: $fizzBuzzRun->initialNumber);
        $this->assertSame(expected: 15, actual: $fizzBuzzRun->finalNumber);
        $this->assertSame(
            expected: '1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz',
            actual: $fizzBuzzRun->fizzBuzz,
        );
    }

    /**
     * Ensures that FizzBuzzRun entities can be deleted.
     * @group persistence
     * @group integration
     * @return void
     */
    public function testFizzBuzzRunCanBeDeleted(): void
    {
        // Arrange: Create and persist an entity
        $fizzBuzzRun = new FizzBuzzRun(1, 15, "1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz");
        $this->entityManager->persist($fizzBuzzRun);
        $this->entityManager->flush();

        // Act: Remove entity
        $this->entityManager->remove($fizzBuzzRun);
        $this->entityManager->flush();

        // Assert: Ensure it no longer exists
        $fizzBuzzRuns = $this->repository->findAll();
        $this->assertCount(0, $fizzBuzzRuns);
    }
}
