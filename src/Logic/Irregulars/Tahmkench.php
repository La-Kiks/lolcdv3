<?php

namespace App\Logic\Irregulars;


use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Tahmkench
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface     $logger,
        private ChampionRepository  $championRepository,
        private SpellRepository     $spellRepository,
    )
    {


    }

    public function createTahmKench(): void
    {
        $URL_CHAMPION = 'https://raw.communitydragon.org/latest/game/data/characters/tahmkench/tahmkench.bin.json';

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPION,
            );
            $content = $response->toArray();

            // TahmKench Tahm Kench 223
            $champion = $this->championRepository->findOneByAlias('TahmKench');

            //R
            $array = $content['Characters/TahmKench/Spells/TahmKenchRWrapperAbility/TahmKenchRWrapper']['mSpell']['mDataValues'][6]['mValues'];
            $arraySliced = array_slice($array, 1, 3);
            $rCooldowns = array_values($arraySliced);

            $rSpell = $this->spellRepository->findOneByChampionAndKey($champion, 'r');
            $rSpell->setCooldowns($rCooldowns);
            $this->spellRepository->save($rSpell);
            $this->spellRepository->flush();

            $this->logger->info('Editing Tahm Kench R');

        } catch (\Exception $e){
            $this->logger->error('Failed to get data from JSON' . $e->getMessage());
        }

    }
}
