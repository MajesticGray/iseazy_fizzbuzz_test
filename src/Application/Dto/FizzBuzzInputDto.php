<?php
declare(strict_types=1);

namespace App\Application\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Infrastructure\ApiPlatform\Processor\FizzBuzzRunProcessor;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This data transfer class contains the input data received by the
 *   API to generate the FizzBuzz sequence.
 */
#[ApiResource(
    operations: [
        /**
         */
        new Post(
            uriTemplate: '/desafio/fizz/buzz',
            name: 'generate_fizzbuzz',
            read: false,
            input: FizzBuzzInputDto::class,
            output: FizzBuzzOutputDto::class,
            processor: FizzBuzzRunProcessor::class,
            normalizationContext: ['groups' => ['fizzbuzz:read']],
        ),
    ],
)]
class FizzBuzzInputDto
{
    #[ApiProperty(required: true, description: 'The first number of the FizzBuzz sequence')]
    #[Assert\NotBlank(message: 'The start value is required.')]
    #[Assert\Type(type: 'integer', message: 'The start value must be an integer.')]
    public int $start;

    #[ApiProperty(required: true, description: 'The last number of the FizzBuzz sequence')]
    #[Assert\NotBlank(message: 'The end value is required.')]
    #[Assert\Type(type: 'integer', message: 'The end value must be an integer.')]
    public int $end;
}
