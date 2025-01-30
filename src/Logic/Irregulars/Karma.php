<?php

namespace App\Logic\Irregulars;

use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Karma
{
    public function __construct(
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
    )
    {

    }

    public function createKarma(): void
    {
            // Step 1 Get Champion - Karma 43
            $champion = $this->championRepository->findOneByAlias('Karma');

            // Add 4th rank to R
            $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'r');
            $cooldowns = $spell->getCooldowns();

            if($next = $this->calculateNextCooldown($cooldowns)){
                $cooldowns[] = $next;
                $spell->setCooldowns($cooldowns);
                $this->spellRepository->save($spell);

                $this->logger->info('Editing Karma R');
            }

            $this->spellRepository->flush();
    }

    private function calculateNextCooldown($array): ?int
    {
        // Secure empty array and abnormally long array
        if(count($array) < 2 || count($array) > 6){
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
