<?php

namespace App\Logic\Irregulars;

use App\Entity\Champion;
use App\Entity\Spell;
use App\Logic\CommunityDragon\SpellDTO;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


// 523 - Aphelios CD are really unique, reducing with level up to 13 and haste
class Aphelios
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
    )
    {

    }


    public function createAphelios(): void
    {
        // Plan is to delete current Q W E & create new Qs for each weapon (5)
        // source : https://leagueoflegends.fandom.com/wiki/Aphelios/LoL
        $champion = $this->championRepository->findOneByAlias('Aphelios');

        // Delete old spells : Weapon Abilites, Phase, Weapon Queue System
        $spellsToDelete = ['Weapon Abilites', 'Phase', 'Weapon Queue System'];

        foreach ($spellsToDelete as $spellName){
            if($spell = $this->spellRepository->findOneByName($spellName)){
                $champion->removeSpell($spell);
                $this->spellRepository->delete($spell);
            }
        }

        // Calibrum - Moonshot - 10 to 8 [10, 9.67, 9.33, 9, 8.67, 8.33, 8]
        $this->createApheliosSpell(
            image: 'https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/calibrum_l.png',
            name: 'Moonshot',
            cooldowns: [10, 9.67, 9.33, 9, 8.67, 8.33, 8],
            patch: 'latest',
            key: 'q',
            champion: $champion
        );
        // Severum - Onslaught - 10 to 8 [10, 9.67, 9.33, 9, 8.67, 8.33, 8]
        $this->createApheliosSpell(
            image: 'https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/severum_l.png',
            name: 'Onslaught',
            cooldowns: [10, 9.67, 9.33, 9, 8.67, 8.33, 8],
            patch: 'latest',
            key: 'q',
            champion: $champion
        );
        // Gravitum - Blinding Eclipse - 12 to 10 [12, 11.67, 11.33, 11, 10.67, 10.33, 10]
        $this->createApheliosSpell(
            image: 'https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/gravitum_l.png',
            name: 'Blinding Eclipse',
            cooldowns: [12, 11.67, 11.33, 11, 10.67, 10.33, 10],
            patch: 'latest',
            key: 'q',
            champion: $champion
        );
        // Infernum - Duskwave - 9 to 6 [9, 8.5, 8, 7.5, 7, 6.5, 6]
        $this->createApheliosSpell(
            image: 'https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/infernum_l.png',
            name: 'Duskwave',
            cooldowns: [9, 8.5, 8, 7.5, 7, 6.5, 6],
            patch: 'latest',
            key: 'q',
            champion: $champion
        );
        // Crescendum - Sentry - 9 to 6 [9, 8.5, 8, 7.5, 7, 6.5, 6]
        $this->createApheliosSpell(
            image: 'https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/crescendum_l.png',
            name: 'Sentry',
            cooldowns: [9, 8.5, 8, 7.5, 7, 6.5, 6],
            patch: 'latest',
            key: 'q',
            champion: $champion
        );

        $this->spellRepository->flush();
    }

    private function createApheliosSpell(string $image, string $name, array $cooldowns, string $patch, string $key, Champion $champion):void
    {
        if(!$this->spellRepository->findOneByName($name)){
            $spell = new Spell();
            $spell->setImage($image)
                ->setName($name)
                ->setCooldowns($cooldowns)
                ->setPatch($patch)
                ->setKeyboard($key)
                ->addChampion($champion)
            ;

            $this->spellRepository->save($spell);
            $this->logger->info(sprintf('Spell created : %s .', $name));
        }

    }
}
