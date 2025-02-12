<?php

namespace App\Logic\Irregulars;

use App\Repository\ChampionRepository;
use App\Repository\PatchRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;

class AffectedByCdr
{
    public function __construct(
        private ChampionRepository $championRepository,
        private SpellRepository    $spellRepository,
        private PatchRepository $patchRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function updateNotAffectedByCdr():void
    {
        $notAffectedByCdr = [
            "Amumu" => "w",
            "Bel'Veth" => "q",
            "Jinx" => "q",
            "K'Sante" => "q",
            "Karthus" => "e",
            "Rek'Sai" => "w",
            "Samira" => "r",
            "Singed" => "q",
            "Urgot" => "w",
            "Yasuo" => "q",
            "Yone" => "w",
            "Yuumi" => "w",
            "Zeri" => "q",
        ];

        $loop = 0;
        $updated = 0;
        $champsList = [];
        $spellsList = [];
        foreach ($notAffectedByCdr as $championName => $keyboard){
            $champion = $this->championRepository->findOneByName($championName);

            if($champion) {
                $spell = $this->spellRepository->findOneByChampionAndKey(champion: $champion, key: $keyboard);

                $champsList[] = $champion->getName();
                $spellsList[] = $spell->getName();

                if ($spell->isAffectedByCdr()){
                    $spell->setAffectedByCdr(false);
                    $this->spellRepository->save($spell);
                    $updated++;
                }

            }
            $loop++;
        }
        $this->spellRepository->flush();

        $this->logger->info(sprintf('%s %s', 'Champions list :', implode(", ", $champsList)));
        $this->logger->info(sprintf('%s %s', 'Spells list :', implode(", ", $spellsList)));
        $this->logger->info(sprintf('%s %s %s %s', 'Loop :', $loop, 'Updated : ', $updated));
    }
}
