<?php

namespace App\PriceModifiers;

class PriceModifierResult
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly int $amount,
        public readonly int $new_total
    ) {}
}
