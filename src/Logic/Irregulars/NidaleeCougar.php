<?php

namespace App\Logic\Irregulars;

use App\Entity\Spell;
use App\Logic\CommunityDragon\SpellDTO;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// TODO : Fix Patch input for spell database
// TODO : Find a way to automatize the Spider W cooldowns (hardcoded atm).
class NidaleeCougar
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
    )
    {

    }

    public function createNidaleeCougar(): void
    {
        $URL_NIDALEE = "https://raw.communitydragon.org/latest/game/data/characters/nidalee/nidalee.bin.json";

        try {
            $response = $this->client->request(
                'GET',
                $URL_NIDALEE,
            );

            $content = $response->toArray();

            // Step 1 Get Champion - Nidalee 76
            $championNidalee = $this->championRepository->findOneByAlias('Nidalee');

            //Step 2 prepare  spells
            // Q
            $array = $content['Characters/Nidalee/Spells/Takedown']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 5);
            $qCooldowns = array_values($arraySliced);

            $qSpell = new SpellDTO(
                champion: 'Nidalee',
                customId: 76,
                name: $content['Characters/Nidalee/Spells/Takedown']['mScriptName'],
                key: 'q',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/nidalee/hud/icons2d/nidalee_q2.png',
                cooldowns: $qCooldowns
            );
            // W
            $array = $content['Characters/Nidalee/Spells/Pounce']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 5);
            $wCooldowns = array_values($arraySliced);

            $wSpell = new SpellDTO(
                champion: 'Nidalee',
                customId: 76,
                name: $content['Characters/Nidalee/Spells/Pounce']['mScriptName'],
                key: 'w',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/nidalee/hud/icons2d/nidalee_w2.png',
                cooldowns: $wCooldowns
            );
            // E
            $array = $content['Characters/Nidalee/Spells/Swipe']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 5);
            $eCooldowns = array_values($arraySliced);

            $eSpell = new SpellDTO(
                champion: 'Nidalee',
                customId: 76,
                name: $content['Characters/Nidalee/Spells/Swipe']['mScriptName'],
                key: 'e',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/nidalee/hud/icons2d/nidalee_e2.png',
                cooldowns: $eCooldowns
            );

            // Step 3, add spells to DB.
            $spellsArray = array($qSpell, $wSpell, $eSpell);

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
                        ->addChampion($championNidalee)
                    ;

                    $championNidalee->addSpell($spell);

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
