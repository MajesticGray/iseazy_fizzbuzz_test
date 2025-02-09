<?php
declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\Dto\FizzBuzzInputDto;
use App\Application\Dto\FizzBuzzOutputDto;
use App\Domain\Model\FizzBuzz as FizzBuzzModel;
use App\Domain\Service\FizzBuzzGenerator;
use App\Infrastructure\Doctrine\Entity\FizzBuzzRun;
use App\Infrastructure\Persistence\Repository\FizzBuzzRunRepository;
use Exception;

/**

 */
class FizzBuzzRunProcessor implements ProcessorInterface
{
    public function __construct(
        private FizzBuzzRunRepository $fizzBuzzRunRepository,
        private FizzBuzzGenerator $fizzBuzzGenerator,
    ) {
    }

    /**
     * Summary of process
     * @param FizzBuzzInputDto $data
     * @param \ApiPlatform\Metadata\Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @throws \Exception
     * @return never
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): FizzBuzzModel
    {
        if (!$data instanceof FizzBuzzInputDto) {
            throw new Exception('Attempted to process wrong data type');
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

    private function convertToDomainModel(FizzBuzzRun $entity): FizzBuzzModel
    {
        return new FizzBuzzModel(
            start: $entity->initialNumber,
            end: $entity->finalNumber,
            fizzBuzz: $entity->fizzBuzz,
            createdAt: $entity->createdAt,
        );
    }

    /*
    private function convertToDto(FizzBuzzRun $entity): FizzBuzzOutputDto
    {
        return new FizzBuzzOutputDto(
            start: $entity->initialNumber,
            end: $entity->finalNumber,
            fizzBuzz: $entity->fizzBuzz,
            createdAt: $entity->createdAt,
        );
    }
        */

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Generate FizzBuzz sequence
        die('provide');
        $fizzBuzzSequence = $this->fizzBuzzGenerator->generate($start, $end);
        $fizzBuzzString   = implode(' ', $fizzBuzzSequence);

        // Persist the result
        /*
        $fizzBuzzRun = new FizzBuzzRun(initialNumber: $start, finalNumber: $end, fizzBuzz: $fizzBuzzString);
        $this->repository->save($fizzBuzzRun);
*/
        // Return DTO for API response
        return new FizzBuzzOutputDto(
            $start,
            $end,
            $fizzBuzzString,
            $fizzBuzzRun->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
