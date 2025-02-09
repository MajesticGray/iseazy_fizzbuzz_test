<?php
declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Normalizer;

use App\Application\Dto\FizzBuzzOutputDto;
use App\Domain\Model\FizzBuzz;
use Override;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FizzBuzzNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function getSupportedTypes($format): array
    {
        return [
            'object'        => null,
            '*'             => false,
            FizzBuzz::class => true,
        ];
    }

    #[Override]
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $dto = $this->convertToDto($object);

        return $this->normalizer->normalize($dto, $format, $context + [__CLASS__ => true]);
    }

    private function convertToDto(FizzBuzz $fizzBuzzModel): FizzBuzzOutputDto
    {
        return new FizzBuzzOutputDto(
            start: $fizzBuzzModel->start,
            end: $fizzBuzzModel->end,
            fizzBuzz: $fizzBuzzModel->fizzBuzz,
            createdAt: $fizzBuzzModel->createdAt,
        );
    }

    #[Override]
    public function supportsNormalization($data, ?string $format = null): bool
    {
        return $data instanceof FizzBuzz && !isset($context[__CLASS__]);
    }
}
