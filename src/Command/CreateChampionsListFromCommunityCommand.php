<?php

namespace App\Command;

use App\Logic\CommunityDragon\ChampionsId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-champions-list-from-community',
    description: 'Attempt to reach Community Dragon latest list of champions ID and try to create a champion entry for each one through their JSON files.',
)]
class CreateChampionsListFromCommunityCommand extends Command
{
    public function __construct(
        private ChampionsId $championsId
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->championsId->createChampionsFromScratch();

        $io->success('Command executed successfully');

        return Command::SUCCESS;
    }
}
