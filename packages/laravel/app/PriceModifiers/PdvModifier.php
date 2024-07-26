<?php

namespace App\PriceModifiers;

use App\Models\LineItem;

class PdvModifier implements PriceModifier
{
    public function __construct(private float $rate = 0.25) {}

    public function applies(array $line_items, int $total): bool
    {
        return true;
    }

    public function apply(array $line_items, int $total): PriceModifierResult
    {
        $subtotal = array_reduce(
            $line_items,
            fn (int $acc, LineItem $line_item) => $acc + ($line_item->quantity * $line_item->price),
            0
        );

        return new PriceModifierResult(
            'PDV',
            'PDV '.($this->rate * 100).'%: +'.round($subtotal * $this->rate),
            round($subtotal * $this->rate),
            round($total + $subtotal * $this->rate),
        );
    }
}
