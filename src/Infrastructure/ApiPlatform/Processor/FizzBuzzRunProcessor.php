<?php
declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Dto\FizzBuzzInputDto;
use App\Domain\Exception\FizzBuzzException;
use App\Domain\Model\FizzBuzz as FizzBuzzModel;
use App\Domain\Service\FizzBuzzGenerator;
use App\Infrastructure\Doctrine\Entity\FizzBuzzRun;
use App\Infrastructure\Persistence\Repository\FizzBuzzRunRepository;
use Override;

/**
 * This class is the intermediate middleware between the API entry point
 *   and Domain business logic. It's responsible for processing the input
 *   data and calling the appropriate service. It also persists the results
 *   into the database and returns a Model representation back to the API
 */
class FizzBuzzRunProcessor implements ProcessorInterface
{
    public function __construct(
        private FizzBuzzRunRepository $fizzBuzzRunRepository,
        private FizzBuzzGenerator $fizzBuzzGenerator,
    ) {
    }

    /**
     * Takes the FizzBuzzInputDto input data, calls the FizzBuzz sequence generator and stores the sequence
     * Returns the Domain model instance (FizzBuzzModel) representing the data
     *
     * @param FizzBuzzInputDto $data
     * @param \ApiPlatform\Metadata\Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @throws FizzBuzzException
     * @return FizzBuzzModel
     */
    #[Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): FizzBuzzModel
    {
        if (!$data instanceof FizzBuzzInputDto) {
            throw new FizzBuzzException('Attempted to process wrong data type');
        }

        $sequence       = $this->fizzBuzzGenerator->generate($data->start, $data->end);
        $fizzBuzzString = implode(', ', iterator_to_array($sequence));

        $runEntity = new FizzBuzzRun(
            initialNumber: $data->start,
            finalNumber: $data->end,
            fizzBuzz: $fizzBuzzString,
        );
        $this->fizzBuzzRunRepository->save($runEntity);

        return $this->convertToDomainModel($runEntity);
    }

    /**
     * Converts the Doctrine entity into the invariable Domain Model
     * @param \App\Infrastructure\Doctrine\Entity\FizzBuzzRun $entity
     * @return FizzBuzzModel
     */
    private function convertToDomainModel(FizzBuzzRun $entity): FizzBuzzModel
    {
        return new FizzBuzzModel(
            start: $entity->initialNumber,
            end: $entity->finalNumber,
            fizzBuzz: $entity->fizzBuzz,
            createdAt: $entity->createdAt,
        );
    }
}
