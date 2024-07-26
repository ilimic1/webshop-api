<?php

namespace App\PriceModifiers;

class DiscountModifier implements PriceModifier
{
    public function __construct(
        private int $threshold = 100_00,
        private float $discount = 0.10
    ) {}

    public function applies(array $line_items, int $total): bool
    {
        return $total > $this->threshold;
    }

    public function apply(array $line_items, int $total): PriceModifierResult
    {
        return new PriceModifierResult(
            'Discount',
            'Discount '.($this->discount * 100).'%: '.round(-1 * $total * $this->discount),
            round(-1 * $total * $this->discount),
            round($total - $total * $this->discount),
        );
    }
}
