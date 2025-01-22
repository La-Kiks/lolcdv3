<?php

namespace App\Form;

class SearchChampionDTO
{
    public function __construct(
        public string $nameOne = '',
        public ?int $hasteOne = null,

        public ?string $nameTwo = '',
        public ?int $hasteTwo = null,

        public ?string $nameThree = '',
        public ?int $hasteThree = null,

        public ?string $nameFour = '',
        public ?int $hasteFour = null,

        public ?string $nameFive = '',
        public ?int $hasteFive = null,

        public ?string $nameSix = '',
        public ?int $hasteSix = null,

        public ?string $nameSeven = '',
        public ?int $hasteSeven = null,

        public ?string $nameEight = '',
        public ?int $hasteEight = null,

        public ?string $nameNine = '',
        public ?int $hasteNine = null,

        public ?string $nameTen = '',
        public ?int $hasteTen = null,
    ) {}

}
