<?php

namespace App\Logic\Irregulars;

use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Psr\Log\LoggerInterface;

class AffectedByCdr
{
    public function __construct(
        private ChampionRepository $championRepository,
        private SpellRepository    $spellRepository,
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
        $valid = 0;
        foreach ($notAffectedByCdr as $championName => $keyboard){
            $champion = $this->championRepository->findOneByName($championName);

            if($champion) {
                $this->logger->info(sprintf('%s %s', 'Found champion :', $champion->getName()));
                $spell = $this->spellRepository->findOneByChampionAndKey(champion: $champion, key: $keyboard);
                $this->logger->info(sprintf('%s %s', 'Found spell :', $spell->getName()));
                $spell->setAffectedByCdr(false);
                $this->spellRepository->save($spell);
                $valid++;
            }
            $loop++;
        }
        $this->spellRepository->flush();
        $this->logger->info(sprintf('%s %s %s %s', 'Loop :', $loop, 'Valid : ', $valid));
    }
}
