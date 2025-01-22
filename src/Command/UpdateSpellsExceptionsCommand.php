<?php

namespace App\Command;

use App\Logic\Irregulars\AffectedByCdr;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-spells-exceptions',
    description: 'Update spells exception.',
)]
class UpdateSpellsExceptionsCommand extends Command
{
    public function __construct(
        private readonly AffectedByCdr $affectedByCdr,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->affectedByCdr->updateNotAffectedByCdr();

        $io->success('Command executed successfully');

        return Command::SUCCESS;
    }
}
