<?php

namespace App\Service;

enum DiscountRate: int
{
    case EARLY_PLANNING_MAX_DISCOUNT = 7;
    case EARLY_PLANNING_MID_DISCOUNT = 5;
    case EARLY_PLANNING_MIN_DISCOUNT = 3;

    public function getValue(): string {
        return $this->value;
    }
}
