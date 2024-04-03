<?php

namespace App\Service;

use App\Exception\ValidationFailedException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class DiscountService
 * @package App\Service
 */
class DiscountService
{
    public function __construct(
        private ValidatorInterface $validator,

        private int $clientAge = 0,

        /** @Assert\Date() */
        private ?string $travelStartDate = null,

        /** @Assert\Date() */
        private ?string $paymentDate = null
    )
    {
        $errors = $this->validator->validate($this);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors[0]);
        }
    }

    /**
     * Считает скидку в % в зависимости от возраста ребенка
     *
     * @return int
     */
    public function countChildDiscount(): int
    {
        $age = $this->clientAge;

        return match(true) {
            $age > 11 && $age < 18 => 10,
            $age > 5 && $age < 12 => 30,
            $age > 2 && $age < 6 => 80,
            default => 0
        };
    }

    /**
     * Считает скидку в % в зависимости от времени начала и оплаты путешествия
     *
     * @return int
     * @throws \Exception
     */
    public function countEarlyPlanningDiscount(): int
    {
        if ($this->travelStartDate && $this->paymentDate) {
            $travelStartDate = new \DateTime($this->travelStartDate);
            $paymentDate = new \DateTime($this->paymentDate);

            foreach (DiscountRuleSet::getRules() as $rule) {
                if ($travelStartDate >= $rule->startDate && $travelStartDate <= $rule->endDate) {
                    $discountRates = $rule->discountRates;
                    $paymentMonth = $paymentDate->format('n');

                    /** @var DiscountRate $value */
                    foreach ($discountRates as $key => $value) {
                        if ($paymentMonth <= $key) {
                            return $value->getValue();
                        }
                    }
                }
            }
        }

        return 0;
    }
}