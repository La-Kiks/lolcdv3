<?php

namespace App\Logic\Irregulars;

use App\Entity\Spell;
use App\Logic\CommunityDragon\SpellDTO;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Reksai
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
    )
    {

    }

    public function createReksai(): void
    {
        $URL_CHAMPION = "https://raw.communitydragon.org/latest/game/data/characters/reksai/reksai.bin.json";

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPION,
            );

            $content = $response->toArray();

            // Step 1 Get Champion - Rek'Sai 421
            $champion = $this->championRepository->findOneByAlias('RekSai');

            //Step 2 prepare  spells
            // Q not available in the files atm
            $qCooldowns = [10, 10, 10, 10, 10];

            $qSpell = new SpellDTO(
                champion: 'RekSai',
                customId: 421,
                name: $content['Characters/RekSai/Spells/RekSaiQBurrowedAbility/RekSaiQBurrowedMis']['mScriptName'],
                key: 'q',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/reksai/hud/icons2d/reksai_q2.png',
                cooldowns: $qCooldowns
            );

            // E
            $array = $content['Characters/RekSai/Spells/RekSaiEAbility/RekSaiE']['mSpell']['mDataValues'][12]['mValues'];
            $arraySliced = array_slice($array, 1, 5);
            $eCooldowns = array_values($arraySliced);

            $eSpell = new SpellDTO(
                champion: 'RekSai',
                customId: 421,
                name: $content['Characters/RekSai/Spells/RekSaiEAbility/RekSaiE']['mScriptName'],
                key: 'e',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/reksai/hud/icons2d/reksai_e2.png',
                cooldowns: $eCooldowns
            );

            // Step 3, add spells to DB.
            $spellsArray = array($qSpell, $eSpell);

            /** @var SpellDTO $item */
            foreach ($spellsArray as $item){
                // Verify that the spell DOES NOT exist in the db
                if( !$this->spellRepository->findOneByName($item->name)){
                    $spell = new Spell();
                    $spell->setImage($item->imageUrl)
                        ->setName($item->name)
                        ->setCooldowns($item->cooldowns)
                        ->setPatch('latest')
                        ->setKeyboard($item->key)
                        ->addChampion($champion)
                    ;

                    $champion->addSpell($spell);

                    $this->spellRepository->save($spell);

                    $this->logger->info(sprintf('%s %s.', 'Saving new spell', $item->name));
                }

            }

            $this->spellRepository->flush();

        } catch (\Exception $e){
            $this->logger->error('Failed to get data from JSON' . $e->getMessage());
        }
    }
}
