<?php

namespace App\Service;

use DateTime;

class DiscountRuleSet
{
    public static function getRules(): array
    {
        return [
            new DiscountRule(
                new DateTime('01.10.2024'),
                new DateTime('14.01.2025'),
                [
                    // Март
                    3 => DiscountRate::EARLY_PLANNING_MAX_DISCOUNT,
                    // Апрель
                    4 => DiscountRate::EARLY_PLANNING_MID_DISCOUNT,
                    // Май
                    5 => DiscountRate::EARLY_PLANNING_MIN_DISCOUNT,
                ]
            ),
            new DiscountRule(
                new DateTime('15.01.2025'),
                new DateTime('31.03.2025'),
                [
                    // Август
                    8 => DiscountRate::EARLY_PLANNING_MAX_DISCOUNT,
                    // Сентябрь
                    9 => DiscountRate::EARLY_PLANNING_MID_DISCOUNT,
                    // Октябрь
                    10 => DiscountRate::EARLY_PLANNING_MIN_DISCOUNT,
                ]
            ),
            new DiscountRule(
                new DateTime('01.04.2025'),
                new DateTime('30.09.2025'),
                [
                    // Январь
                    1 => DiscountRate::EARLY_PLANNING_MIN_DISCOUNT,
                    // Ноябрь
                    11 => DiscountRate::EARLY_PLANNING_MAX_DISCOUNT,
                    // Декабрь
                    12 => DiscountRate::EARLY_PLANNING_MID_DISCOUNT,
                ]
            ),
        ];
    }
}