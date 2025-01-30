<?php

namespace App\Logic\Irregulars;


use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;


class Yuumi
{
    public function __construct(
        private LoggerInterface     $logger,
        private ChampionRepository  $championRepository,
        private SpellRepository     $spellRepository,
    )
    {
    }

    public function createYuumi(): void
    {
       $champion = $this->championRepository->findOneByAlias('Yuumi');

       // W
       $cooldowns = [10, 5, 0];
       $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'w');

       $spell->setCooldowns($cooldowns)
           ->setAffectedByCdr(false)
       ;

       $this->spellRepository->save($spell);

       // Q
       $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'q');
       $cooldowns = $spell->getCooldowns();
       // calculating the 6th rank
       $diff = $cooldowns[0] - $cooldowns[1];
       $next = end($cooldowns) - $diff;
       $cooldowns[] = $next;
       $spell->setCooldowns($cooldowns);

       $this->spellRepository->save($spell);

       $this->spellRepository->flush();

       $this->logger->info('Editing Yuumi W');
    }
}
