<?php
declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform\Factory;

use App\Infrastructure\Doctrine\Entity\FizzBuzzRun;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * This factory creates sample Doctrine infrastructure FizzBuzzRun objects to be used in tests
 */
final class FizzBuzzRunFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return FizzBuzzRun::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'initialNumber' => 1,
            'finalNumber'   => 15,
            'fizzBuzz'      => '1, 2, Fizz, 4, Buzz, Fizz, 7, 8, Fizz, Buzz, 11, Fizz, 13, 14, FizzBuzz',
            'createdAt'     => self::faker()->dateTime(),
        ];
    }
}
