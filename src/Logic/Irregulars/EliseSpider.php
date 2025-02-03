<?php

namespace App\Logic\Irregulars;

use App\Entity\Spell;
use App\Logic\CommunityDragon\SpellDTO;
use App\Repository\ChampionRepository;
use App\Repository\PatchRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// TODO : Fix Patch input for spell database
// TODO : Find a way to automatize the Spider W cooldowns (hardcoded atm).
class EliseSpider
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
        private PatchRepository $patchRepository,
    )
    {

    }

    public function createEliseSpider(): void
    {
        $URL_ELISE = "https://raw.communitydragon.org/latest/game/data/characters/elise/elise.bin.json";

        try {
            $response = $this->client->request(
                'GET',
                $URL_ELISE,
            );

            $content = $response->toArray();

            // Step 1 Get Elise Champion - Elise - 60
            $championElise = $this->championRepository->findOneByAlias('Elise');
            $patch = $this->patchRepository->findMostRecentEntry();

            //Step 2 prepare spider spells
            // Q
            $array = $content['Characters/Elise/Spells/EliseSpiderQAbility/EliseSpiderQ']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 5);
            $qCooldowns = array_values($arraySliced);

            $qSpell = new SpellDTO(
                champion: 'Elise',
                customId: 60,
                name: $content['Characters/Elise/Spells/EliseSpiderQAbility/EliseSpiderQ']['mScriptName'],
                key: 'q',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/elise/hud/icons2d/elisespiderq.png',
                cooldowns: $qCooldowns
            );
            // W
            $wSpell = new SpellDTO(
                champion: 'Elise',
                customId: 60,
                name: $content['Characters/Elise/Spells/EliseSpiderWAbility/EliseSpiderW']['mScriptName'],
                key: 'w',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/elise/hud/icons2d/elisespiderw.png',
                cooldowns: [10, 10, 10, 10, 10]
            );
            // E
            $array = $content['Characters/Elise/Spells/EliseSpiderEAbility/EliseSpiderE']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 5);
            $eCooldowns = array_values($arraySliced);

            $eSpell = new SpellDTO(
                champion: 'Elise',
                customId: 60,
                name: $content['Characters/Elise/Spells/EliseSpiderEAbility/EliseSpiderE']['mScriptName'],
                key: 'e',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/elise/hud/icons2d/elisespidere.png',
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
                        ->setPatch($patch)
                        ->setKeyboard($item->key)
                        ->addChampion($championElise)
                    ;

                    $championElise->addSpell($spell);

                    $this->spellRepository->save($spell);

                    $this->logger->info(sprintf('%s %s.', 'Saving new spell', $item->name));
                }

            }

            $this->spellRepository->flush();


        } catch (\Exception $e){
            $this->logger->error('Failed to get data from JSON for Elise' . $e->getMessage());
        }
    }
}
