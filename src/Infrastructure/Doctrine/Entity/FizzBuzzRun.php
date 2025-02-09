<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Infrastructure\Persistence\Repository\FizzBuzzRunRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'fizzbuzz_run')]
#[ORM\Entity(repositoryClass: FizzBuzzRunRepository::class)]
#[ORM\HasLifecycleCallbacks]
class FizzBuzzRun
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public protected(set) ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public protected(set) DateTimeInterface $createdAt;

    public function __construct(
        #[ORM\Column(type: Types::INTEGER)]
        #[Assert\NotBlank]
        public readonly int $initialNumber,
        #[ORM\Column(type: Types::INTEGER)]
        #[Assert\NotBlank]
        public readonly int $finalNumber,
        #[ORM\Column(type: Types::TEXT)]
        #[Assert\NotBlank]
        public readonly string $fizzBuzz,
    ) {
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

}
