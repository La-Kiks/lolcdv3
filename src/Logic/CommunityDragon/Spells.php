<?php

namespace App\Logic\CommunityDragon;

use App\Entity\Champion;
use App\Entity\Spell;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Spells
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
    )
    {
    }

    public function aurora(): array
    {
        $URL = "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/893.json";
        $caitUrl = "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/51.json";

        $dtos = [];

        try {
            $response = $this->client->request(
                'GET',
                $caitUrl
            );

            $content = $response->toArray();

            $championAlias = strtolower($content['alias']);
            $championId = $content['id'];
            $baseImgUrl = sprintf('%s%s%s',
                "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/assets/characters/",
                $championAlias,
                "/hud/icons2d/"
            );


            // Spell infos : name, key, image, cooldowns[], champion, patch
            foreach ($content['spells'] as $spell){
                $name = $spell['name'];
                $key = $spell['spellKey'];
                $image = sprintf('%s%s',
                    $baseImgUrl,
                    strtolower(basename($spell['abilityIconPath']))
                );
                $cooldowns = [];

                // TESTING AMMOs -> Seems Good
                if($spell['ammo']['ammoRechargeTime'][0] == 0){
                    $cooldownsArray = $spell['cooldownCoefficients'];
                } else {
                    $cooldownsArray = $spell['ammo']['ammoRechargeTime'];
                }
                //
                foreach ($cooldownsArray as $cooldown){
                    $cooldowns[] = $cooldown;
                }


                $spellDTO = new SpellDTO(
                    champion: $championAlias,
                    customId: $championId,
                    name: $name,
                    key: $key,
                    imageUrl: $image,
                    cooldowns: $cooldowns
                );

                $dtos[] = $spellDTO;
            }

        } catch (\Exception $e){
            $this->logger->error('Failed to reach the url. ' . $e);
        }

        return $dtos ;
    }
    public function createSpells(): void
    {
        // Step 1 get all the champions from database
        $champions = $this->championRepository->findAll();

        $baseUrl = "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/";

        // Step 2 loop through each champion
        foreach ($champions as $champion){
            $url = sprintf('%s%s%s', $baseUrl, $champion->getCustomId(), '.json');

            try {
                $response = $this->client->request(
                    'GET',
                    $url,
                );

                $content = $response->toArray();

                $championAlias = strtolower($content['alias']);
                $championId = $content['id'];
                $baseImgUrl = sprintf('%s%s%s',
                    "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/assets/characters/",
                    $championAlias,
                    "/hud/icons2d/"
                );

                foreach ($content['spells'] as $spell){
                    $name = $spell['name'];
                    $key = $spell['spellKey'];
                    $image = sprintf('%s%s',
                        $baseImgUrl,
                        strtolower(basename($spell['abilityIconPath']))
                    );
                    $cooldowns = [];

                    //
                    if($spell['ammo']['ammoRechargeTime'][0] == 0 ){
                        $cooldownsArray = $spell['cooldownCoefficients'];
                    } else {
                        $cooldownsArray = $spell['ammo']['ammoRechargeTime'];
                    }
                    // TODO : R cooldowns should be 1st 3 elements
                    // TODO : Every spell cooldowns should be first 5 elements
                    // Few exceptions that can maybe be handle separately, Jayce, Udyr, Yuumi Q...
                    foreach ($cooldownsArray as $cooldown){
                        $cooldowns[] = $cooldown;
                    }

                    $spellDTO = new SpellDTO(
                        champion: $championAlias,
                        customId: $championId,
                        name: $name,
                        key: $key,
                        imageUrl: $image,
                        cooldowns: $cooldowns
                    );

                    // TODO : Check if the spell already exists in the DB
                    // If the spell exits I want to compare the cooldown arrays & update them prolly
                    // Or update if the patch is different
                    if(!$this->spellRepository->exists($spellDTO->name)){
                        $newSpell = new Spell();
                        $newSpell->setImage($spellDTO->imageUrl)
                            ->setName($spellDTO->name)
                            ->setCooldowns($spellDTO->cooldowns)
                            ->setPatch('Latest')
                            ->setKeyboard($spellDTO->key)
                            ->addChampion($champion)
                        ;

                        $champion->addSpell($newSpell);

                        $this->spellRepository->save($newSpell);
                    }

                }

                $this->spellRepository->flush();

            } catch (\Exception $e) {
                $this->logger->error(
                    sprintf('%s %s %s %s',
                        'Failed to reach the url for this champion',
                        $champion->getName(),
                        'Error : ',
                        $e
                    )
                );
            }
        }
    }

    // Maybe can be used to not render spells with zero CDs with tweaks
    public function findZeroCd(): array
    {
        $array = [];
        $i = 0;
        $spells = $this->spellRepository->findAll();

        foreach ($spells as $spell){
            $cds = $spell->getCooldowns();

            if(array_unique($cds) == [0]){
                $champ = $this->championRepository->findOneBySpell($spell);
                $champion = $champ[0];

                if($champion instanceof Champion){
                    $name = $champion->getName();
                } else {
                    $name = sprintf('%s %s', 'unnamed', $i);
                }

                $array[$name] = $spell->getName();
            }
            $i++;
        }

        return $array;
    }
}
