<?php

namespace App\Command;

use App\Logic\Irregulars\AffectedByCdr;
use App\Logic\Irregulars\AurelionSol;
use App\Logic\Irregulars\EliseSpider;
use App\Logic\Irregulars\Jayce;
use App\Logic\Irregulars\NidaleeCougar;
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
        private readonly EliseSpider $eliseSpider,
        private readonly NidaleeCougar $nidaleeCougar,
        private readonly AurelionSol $aurelionSol,
        private readonly Jayce $jayce,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // $this->affectedByCdr->updateNotAffectedByCdr();

        // $this->eliseSpider->createEliseSpider();

        // $this->nidaleeCougar->createNidaleeCougar();

        // $this->aurelionSol->createAurelionSol();

        // $this->jayce->createJayce();

        $io->success('Command executed successfully');

        return Command::SUCCESS;
    }
}
