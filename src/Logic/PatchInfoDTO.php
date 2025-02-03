<?php

namespace App\Logic;

class PatchInfoDTO
{
    public function __construct(
        public bool $toUpdate,
        public string $numero,
    )
    {

    }

}
