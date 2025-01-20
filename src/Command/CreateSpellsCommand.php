<?php

namespace App\Command;

use App\Logic\CommunityDragon\ChampionsId;
use App\Logic\CommunityDragon\Spells;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-spells-list',
    description: 'Attempt to reach Community Dragon latest list of spells & try to create them in the DB.',
)]
class CreateSpellsCommand extends Command
{
    public function __construct(
        private Spells $spells,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->spells->createSpells();

        $io->success('Command executed successfully');

        return Command::SUCCESS;
    }
}
