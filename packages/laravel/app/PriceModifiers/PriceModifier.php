<?php

namespace App\PriceModifiers;

interface PriceModifier
{
    /**
     * Determines if this modifier should apply to the order.
     */
    public function applies(array $line_items, int $total): bool;

    /**
     * Apply price modification.
     */
    public function apply(array $line_items, int $total): PriceModifierResult;
}
