<?php
declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Service\FizzBuzzGenerator;
use Exception;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fizzbuzz:generate',
    description: 'Generates the fizzbuzz sequence between <start> and <end>',
)]
class FizzBuzzCommand extends Command
{
    public function __construct(
        private FizzBuzzGenerator $fizzBuzzGenerator,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->addArgument('start', InputArgument::REQUIRED, 'The initial number')
            ->addArgument('end', InputArgument::REQUIRED, 'The final number');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = (int)$input->getArgument('start');
        $end   = (int)$input->getArgument('end');

        try {
            $sequence = $this->fizzBuzzGenerator->generate($start, $end);
            $this->dumpSequence($output, $sequence);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln(sprintf(
                '%s: %s',
                $e::class,
                $e->getMessage(),
            ));
        }

        return Command::FAILURE;
    }

    private function dumpSequence(OutputInterface $output, iterable $sequence): void
    {
        foreach ($sequence as $sequenceItem) {
            $output->writeln($sequenceItem);
        }
    }
}
