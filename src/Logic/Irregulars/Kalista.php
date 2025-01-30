<?php

namespace App\Logic\Irregulars;


use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Kalista
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface     $logger,
        private readonly ChampionRepository  $championRepository,
        private readonly SpellRepository     $spellRepository,
    )
    {
    }

    public function createKalista(): void
    {
        $URL_CHAMPION = 'https://raw.communitydragon.org/latest/game/data/characters/kalista/kalista.bin.json';

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPION,
            );
            $content = $response->toArray();

            // Kalista 429
            $champion = $this->championRepository->findOneByAlias('Kalista');

            // E
            $array = $content['Characters/Kalista/Spells/KalistaExpungeWrapperAbility/KalistaExpungeWrapper']['mSpell']['mDataValues'][8]['mValues'];
            $arraySliced = array_slice($array, 1, 5);
            $cooldowns = array_values($arraySliced);

            $spell = $this->spellRepository->findOneByChampionAndKey($champion, 'e');
            $spell->setCooldowns($cooldowns);
            $this->spellRepository->save($spell);

            $this->spellRepository->flush();

            $this->logger->info('Editing Kalista E');

        } catch (\Exception $e){
            $this->logger->error('Failed to get data from JSON' . $e->getMessage());
        }
    }
}
