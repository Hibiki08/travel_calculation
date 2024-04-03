<?php

namespace App\Service;

use DateTime;

class DiscountRule
{
    public function __construct(
        public DateTime $startDate,
        public DateTime $endDate,
        public array $discountRates
    ) {}
}