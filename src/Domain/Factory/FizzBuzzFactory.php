<?php
declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Model\FizzBuzz;
use Override;
use Zenstruck\Foundry\Factory;

/**
 * This factory creates sample Domain FizzBuzz models to be used in tests
 */
final class FizzBuzzFactory extends Factory
{
    #[Override]
    public function create(array|callable $attributes = []): mixed
    {
        if (is_callable($attributes)) {
            $attributes = $attributes();
        }
        $attributes = array_merge($this->defaults(), $attributes);

        return new FizzBuzz(
            start: $attributes['start'],
            end: $attributes['end'],
            fizzBuzz: $attributes['fizzBuzz'],
            createdAt: $attributes['createdAt'],
        );
    }

    #[Override]
    protected function defaults(): array|callable
    {
        return [
            'start'     => 1,
            'end'       => 15,
            'fizzBuzz'  => '1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz',
            'createdAt' => self::faker()->dateTime(),
        ];
    }
}
