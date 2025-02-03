<?php

namespace App\Logic\Irregulars;

use App\Entity\Spell;
use App\Logic\CommunityDragon\SpellDTO;
use App\Repository\ChampionRepository;
use App\Repository\PatchRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class Jayce
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

    public function createJayce(): void
    {
        $URL_CHAMPION = "https://raw.communitydragon.org/latest/game/data/characters/jayce/jayce.bin.json";

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPION,
            );

            $content = $response->toArray();

            // Step 1 Get Champion - Jayce 126
            $champion = $this->championRepository->findOneByAlias('Jayce');
            $patch = $this->patchRepository->findMostRecentEntry();

            // Edit Q, W E cd to length 6
            // To the Skies! / Shock Blast
            $currentQSpell = $this->spellRepository->findOneByName('To the Skies! / Shock Blast');
            $currentQCooldowns = $currentQSpell->getCooldowns();

            if($sixthQ = $this->sixthElement($currentQCooldowns)){
                $currentQCooldowns[] = $sixthQ;
                $currentQSpell->setCooldowns($currentQCooldowns);
                $this->spellRepository->save($currentQSpell);
            };

            // Lightning Field / Hyper Charge
            $currentWSpell = $this->spellRepository->findOneByName('Lightning Field / Hyper Charge');
            $currentWCooldowns = $currentWSpell->getCooldowns();

            if($sixthW = $this->sixthElement($currentWCooldowns)){
                $currentWCooldowns[] = $sixthW;
                $currentWSpell->setCooldowns($currentWCooldowns);
                $this->spellRepository->save($currentWSpell);
            }

            // Thundering Blow / Acceleration Gate
            $currentESpell = $this->spellRepository->findOneByName('Thundering Blow / Acceleration Gate');
            $currentECooldowns = $currentESpell->getCooldowns();

            if($sixthE = $this->sixthElement($currentECooldowns)){
                $currentECooldowns[] = $sixthE;
                $currentESpell->setCooldowns($currentECooldowns);
                $this->spellRepository->save($currentESpell);
            }

            // R To edit to one Rank => 1 CD
            $currentRSpell = $this->spellRepository->findOneByChampionAndKey($champion, 'r');
            $rCooldowns = $currentRSpell->getCooldowns();
            $currentRSpell->setCooldowns(array_slice($rCooldowns,0,1))
                ->setPatch($patch)
            ;

            $this->spellRepository->save($currentRSpell);

            //Step 2 prepare new spells
            // Q
            $array = $content['Characters/Jayce/Spells/JayceShockBlastAbility/JayceShockBlast']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 6);
            $qCooldowns = array_values($arraySliced);

            $qSpell = new SpellDTO(
                champion: 'Jayce',
                customId: 126,
                name: $content['Characters/Jayce/Spells/JayceShockBlastAbility/JayceShockBlast']['mScriptName'],
                key: 'q',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/jayce/hud/icons2d/jayceq_ranged.png',
                cooldowns: $qCooldowns
            );

            // W
            $array = $content['Characters/Jayce/Spells/JayceHyperChargeAbility/JayceHyperCharge']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 6);
            $wCooldowns = array_values($arraySliced);

            $wSpell = new SpellDTO(
                champion: 'Jayce',
                customId: 126,
                name: $content['Characters/Jayce/Spells/JayceHyperChargeAbility/JayceHyperCharge']['mScriptName'],
                key: 'w',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/jayce/hud/icons2d/jaycew_ranged.png',
                cooldowns: $wCooldowns
            );

            // E
            $array = $content['Characters/Jayce/Spells/JayceAccelerationGateAbility/JayceAccelerationGate']['mSpell']['cooldownTime'];
            $arraySliced = array_slice($array, 1, 6);
            $eCooldowns = array_values($arraySliced);

            $eSpell = new SpellDTO(
                champion: 'Jayce',
                customId: 126,
                name: $content['Characters/Jayce/Spells/JayceAccelerationGateAbility/JayceAccelerationGate']['mScriptName'],
                key: 'e',
                imageUrl: 'https://raw.communitydragon.org/latest/game/assets/characters/jayce/hud/icons2d/jaycee_ranged.png',
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

    private function sixthElement($array): ?int
    {
        if(count($array) != 5){
            return null;
        }

        if($array[0] == $array[1]){
            $sixth = $array[0];
        } else {
            $diff = ($array[0] - $array[1]);
            $sixth = ($array[4] - $diff);
        }

        return $sixth;

    }
}
