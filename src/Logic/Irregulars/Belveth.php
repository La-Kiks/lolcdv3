<?php

namespace App\Logic\Irregulars;


use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Belveth
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface     $logger,
        private readonly ChampionRepository  $championRepository,
        private readonly SpellRepository     $spellRepository,
    )
    {
    }

    public function createBelveth(): void
    {
        $URL_CHAMPION = 'https://raw.communitydragon.org/latest/game/data/characters/belveth/belveth.bin.json';

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPION,
            );
            $content = $response->toArray();

            // Bel'Veth BelVeth 200
            $champion = $this->championRepository->findOneByAlias('Belveth');

            // Q
            $array = $content['Characters/Belveth/Spells/BelvethQAbility/BelvethQ']['mSpell']['mDataValues'][0]['mValues'];
            $arraySliced = array_slice($array, 1, 5);
            $cooldowns = array_values($arraySliced);

            $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'q');
            $spell->setCooldowns($cooldowns)
                ->setAffectedByCdr(false)
            ;
            $this->spellRepository->save($spell);

            $this->spellRepository->flush();

            $this->logger->info('Editing BelVeth Q');

        } catch (\Exception $e){
            $this->logger->error('Failed to get data from JSON' . $e->getMessage());
        }
    }
}
