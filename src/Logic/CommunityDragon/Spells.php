<?php

namespace App\Logic\CommunityDragon;

use App\Entity\Champion;
use App\Entity\Spell;
use App\Repository\ChampionRepository;
use App\Repository\PatchRepository;
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
        private PatchRepository $patchRepository,
    )
    {
    }

    public function createOrUpdateSpells(): void
    {
        // Step 1 get all the champions from database
        $champions = $this->championRepository->findAll();
        $patch = $this->patchRepository->findMostRecentEntry();

        $baseUrl = "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/";

        // Trackers
        $spellsCreated = 0;
        $spellsUpdated = 0;

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

                    // Handling case where Ammo is the expected cooldown. If ammo CD < basic CD => choose basic CD
                    // When ammo is present it can be negative, zero or really short. Shorter than base CD.
                    if($spell['ammo']['ammoRechargeTime'][0] < $spell['cooldownCoefficients'][0] ){
                        $cooldownsArray = $spell['cooldownCoefficients'];
                    } else {
                        $cooldownsArray = $spell['ammo']['ammoRechargeTime'];
                    }

                    foreach ($cooldownsArray as $cooldown){
                        $cooldowns[] = $cooldown;
                    }

                    // Making sure basic spells have 5 ranks & ultimates 3 ranks. Exceptions will be handled separately.
                    if ($key === 'r'){
                        array_splice($cooldowns, 3);
                    } else {
                        array_splice($cooldowns, 5);
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
                            ->setPatch($patch)
                            ->setKeyboard($spellDTO->key)
                            ->addChampion($champion)
                        ;

                        $champion->addSpell($newSpell);

                        $this->spellRepository->save($newSpell);
                        $this->logger->info(sprintf('%s %s.', 'Spell created :', $spellDTO->name));
                        $spellsCreated++;

                    } else{
                        $spellToEdit = $this->spellRepository->findOneByName($spellDTO->name);
                        $spellToEditCooldowns = $spellToEdit->getCooldowns();

                        if($spellToEditCooldowns[0] != $spellDTO->cooldowns[0]){
                            $spellToEdit->setCooldowns($spellDTO->cooldowns)
                                ->setPatch($patch)
                            ;

                            $this->spellRepository->save($spellToEdit);
                            $this->logger->info(sprintf('%s %s.', 'Spell updated :', $spellDTO->name));
                            $spellsUpdated++;

                        }
                    }

                }

                $this->spellRepository->flush();

                $this->logger->info(sprintf('Spells info : created %s, updated %s.', $spellsCreated, $spellsUpdated));

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

    // Maybe can be used to not render spells with zero CDs with tweaks, or specify CD = none.
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
