<?php

namespace App\Command;

use App\Entity\Patch;
use App\Logic\CommunityDragon\ChampionsId;
use App\Logic\PatchInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-patch',
    description: 'Update patch versions',
)]
class UpdatePatchCommand extends Command
{
    public function __construct(
        private PatchInfo $patchInfo
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->patchInfo->updatePatch();

        $io->success('Command executed successfully');

        return Command::SUCCESS;
    }
}
