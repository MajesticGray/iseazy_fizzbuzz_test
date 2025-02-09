<?php
declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Infrastructure\Doctrine\Entity\FizzBuzzRun;
use App\Tests\Api\AbstractApiTest;
use Doctrine\ORM\EntityManagerInterface;

class FizzBuzzApiTest extends AbstractApiTest
{
    /**
     * This tests a good API request returns a successful response and the expected data
     * @group api
     * @group functional
     * @return void
     */
    public function testFizzBuzzApiReturnsCorrectResponse(): void
    {
        $response = $this->performJsonApiRequest('POST', '/desafio/fizz/buzz', [
            'start' => 1,
            'end'   => 15,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            headerName: 'content-type',
            expectedValue: 'application/vnd.api+json; charset=utf-8',
        );

        $responseData = $response->toArray(throw: false)['data']['attributes'] ?? [];

        // Ensure all expected keys exist
        $this->assertArrayHasKey(key: 'start', array: $responseData);
        $this->assertArrayHasKey(key: 'end', array: $responseData);
        $this->assertArrayHasKey(key: 'fizzBuzz', array: $responseData);
        $this->assertArrayHasKey(key: 'createdAt', array: $responseData);

        // Validate other fields
        $this->assertSame(expected: 1, actual: $responseData['start']);
        $this->assertSame(expected: 15, actual: $responseData['end']);
        $this->assertSame(
            expected: '1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz',
            actual: $responseData['fizzBuzz'],
        );
    }

    /**
     * This tests a bad API request returns the proper error and response code
     * @group api
     * @group functional
     * @return void
     */
    public function testFizzBuzzApiValidationFailure(): void
    {
        $this->performJsonApiRequest('POST', '/desafio/fizz/buzz', [
            'end' => 15,
        ]);

        $this->assertResponseStatusCodeSame(422);

        $this->assertJsonContains([
            'errors' => [
                [
                    'source' => ['pointer' => 'data/attributes/start'],
                    'detail' => 'The start value is required.',
                ],
            ],
        ]);

        $this->performJsonApiRequest('POST', '/desafio/fizz/buzz', [
            'start' => 1,
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'errors' => [
                [
                    'source' => ['pointer' => 'data/attributes/end'],
                    'detail' => 'The end value is required.',
                ],
            ],
        ]);
    }

    /**
     * This tests a good API request inserts the expected row into the database
     * @group api
     * @group functional
     * @return void
     */
    public function testFizzBuzzApiDatabasePersistence(): void
    {
        $this->performJsonApiRequest('POST', '/desafio/fizz/buzz', [
            'start' => 1,
            'end'   => 15,
        ]);

        /** @var EntityManagerInterface */
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $repository    = $entityManager->getRepository(FizzBuzzRun::class);

        $entities = $repository->findAll();
        // There will be only one, because the db is reset after each test.
        $this->assertCount(1, $entities);

        $fizzBuzzRun = end($entities);
        // Ensure the object insert into the database contains the same data
        //    we have requested to generate
        $this->assertSame(
            expected: '1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz',
            actual: $fizzBuzzRun->fizzBuzz,
        );
        $this->assertSame(
            expected: 1,
            actual: $fizzBuzzRun->initialNumber,
        );
        $this->assertSame(
            expected: 15,
            actual: $fizzBuzzRun->finalNumber,
        );
    }
}
