<?php

namespace App\Logic\Irregulars;

use App\Entity\Spell;
use App\Logic\CommunityDragon\SpellDTO;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class AurelionSol
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
    )
    {

    }

    public function createAurelionSol(): void
    {
        $URL_CHAMPION = "https://raw.communitydragon.org/latest/game/data/characters/aurelionsol/aurelionsol.bin.json";

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPION,
            );

            $content = $response->toArray();

            // Step 1 Get Champion - Aurelion 136
            $champion = $this->championRepository->findOneByAlias('AurelionSol');

            // W
            $array = $content['Characters/AurelionSol/Spells/AurelionSolWAbility/AurelionSolWToggle']['mSpell']['mDataValues'][9]['mValues'];
            $arraySliced = array_slice($array, 1, 5);
            $wCooldowns = array_values($arraySliced);

            $currentWSpell = $this->spellRepository->findOneByChampionAndKey($champion, 'w');

            $currentWSpell->setCooldowns($wCooldowns)
                ->setPatch('latest')
            ;

            $this->spellRepository->save($currentWSpell);
            $this->spellRepository->flush();
            $this->logger->info('Aurelion W updated.');

        } catch (\Exception $e){
            $this->logger->error('Failed to get data from JSON' . $e->getMessage());
        }
    }
}
