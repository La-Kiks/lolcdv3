<?php

namespace App\Logic\CommunityDragon;

readonly class SpellDTO
{
    public function __construct(
        public string $champion,
        public int $customId,
        public string $name,
        public string $key,
        public string $imageUrl,
        public array $cooldowns,
    )
    {

    }
}
