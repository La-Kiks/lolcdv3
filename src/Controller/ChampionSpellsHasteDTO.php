<?php

namespace App\Controller;

use App\Entity\Champion;

class ChampionSpellsHasteDTO
{
    public function __construct(
        public readonly Champion $champion,
        public readonly array $spells,
        public readonly int $haste,
    )
    {
    }
}
