<?php

namespace App\Logic\Irregulars;


use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;


class Udyr
{
    public function __construct(
        private LoggerInterface     $logger,
        private ChampionRepository  $championRepository,
        private SpellRepository     $spellRepository,
    )
    {
    }

    public function createUdyr(): void
    {
        $champion = $this->championRepository->findOneByAlias('Udyr');

        // Q
        $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'q');
        $cooldowns = $spell->getCooldowns();

        while (count($cooldowns) < 6){
            $next = $this->sixthElement($cooldowns);
            $cooldowns[] = $next;
        }

        $spell->setCooldowns($cooldowns);
        $this->spellRepository->save($spell);

        // W
        $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'w');
        $cooldowns = $spell->getCooldowns();

        while (count($cooldowns) < 6){
            $next = $this->sixthElement($cooldowns);
            $cooldowns[] = $next;
        }

        $spell->setCooldowns($cooldowns);
        $this->spellRepository->save($spell);
        // E
        $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'e');
        $cooldowns = $spell->getCooldowns();

        while (count($cooldowns) < 6){
            $next = $this->sixthElement($cooldowns);
            $cooldowns[] = $next;
        }

        $spell->setCooldowns($cooldowns);
        $this->spellRepository->save($spell);

        // R
        $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'r');
        $cooldowns = $spell->getCooldowns();

        while (count($cooldowns) < 6){
            $next = $this->sixthElement($cooldowns);
            $cooldowns[] = $next;
        }

        $spell->setCooldowns($cooldowns);
        $this->spellRepository->save($spell);

        $this->spellRepository->flush();
        $this->logger->info('Editing Udyr spells.');
    }

    private function sixthElement($array): ?int
    {
        if(count($array) <= 2 || count($array) > 9){
            return null;
        }

        if($array[0] == $array[1]){
            $next = $array[0];
        } else {
            $diff = ($array[0] - $array[1]);
            $next = (end($array) - $diff);
        }

        return $next;

    }
}
