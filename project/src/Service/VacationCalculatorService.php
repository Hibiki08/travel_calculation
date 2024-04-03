<?php

namespace App\Service;

use App\Exception\ValidationFailedException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class VacationCalculatorService
 * @package App\Service
 */
class VacationCalculatorService
{
    private DiscountService $discountService;

    #[Assert\PositiveOrZero]
    private int $clientAge = 0;

    const DATE_FORMAT = 'd.m.Y';

    const MAX_CHILD_DISCOUNT = 4500;

    /* минимальный интервал между платежом и началом путешествия в месяцах */
    const MIN_EARLY_PLANNING_INTERVAL = 2;

    /**
     * @param ValidatorInterface $validator
     * @param int|null $basePrice
     * @param string|null $clientBirthDate
     * @param string|null $travelStartDate
     * @param string|null $paymentDate
     * @throws ValidationFailedException
     */
    public function __construct(
        private ValidatorInterface $validator,

        #[Assert\NotBlank]
        #[Assert\Positive]
        private ?int $basePrice = null,

        #[Assert\NotBlank]
        private ?string $clientBirthDate = null,

        private ?string $travelStartDate = null,
        private ?string $paymentDate = null
    ) {
        $this->setDefaultValues();
    }

    /**
     * @return float|int
     * @throws ValidationFailedException
     */
    public function calculate(): float|int
    {
        $errors = $this->validator->validate($this);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors[0]);
        }

        return $this->basePrice - $this->countDiscountSum();
    }

    /**
     * @param array $values
     * @return $this
     * @throws ValidationFailedException
     */
    public function processData(array $values = []): static
    {
        foreach ($values as $key => $value) {
            try {
                $this->$key = $value;
            } catch (\Exception $e) {
                continue;
            }
        }

        $this->setDefaultValues();

        return $this;
    }

    /**
     * @return void
     * @throws ValidationFailedException
     */
    private function setDefaultValues(): void
    {
        if (!$this->travelStartDate) {
            $this->travelStartDate = date(self::DATE_FORMAT);
        }

        $this->clientAge = $this->getClientAge();

        $this->discountService = new DiscountService(
            $this->validator,
            $this->clientAge,
            $this->travelStartDate,
            $this->paymentDate
        );
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function getClientAge(): int
    {
        try {
            $clientBirthDate = new \DateTime($this->clientBirthDate);
            $travelStartDate = new \DateTime($this->travelStartDate);
            $interval = $clientBirthDate->diff($travelStartDate);
            return $interval->y;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function isClientChild(): bool
    {
        return $this->clientAge < 18;
    }

    /**
     * @throws \Exception
     */
    private function countDiscountSum(): float|int
    {
        $discountSum = 0;

        if ($this->isClientChild()) {
            $discount = $this->discountService->countChildDiscount();
            $discountSum = $this->basePrice * $discount / 100;

            if ($this->clientAge > 5 && $this->clientAge < 12) {
                if ($discountSum > self::MAX_CHILD_DISCOUNT) {
                    $discountSum = self::MAX_CHILD_DISCOUNT;
                }
            }

            $this->basePrice = $this->basePrice - $discountSum;
        }

        if ($this->isEarlyPlanning()) {
            $discount = $this->discountService->countEarlyPlanningDiscount();
            $discountSum += $this->basePrice * $discount / 100;
        }

        return $discountSum;
    }

    /**
     * Определяет, раннее ли это планирование
     *
     * @return bool
     * @throws \Exception
     */
    private function isEarlyPlanning(): bool
    {
        if ($this->paymentDate) {
            $paymentDate = new \DateTime($this->paymentDate);
            $travelStartDate = new \DateTime($this->travelStartDate);
            $interval = $paymentDate->diff($travelStartDate);
            if ($interval->m >= self::MIN_EARLY_PLANNING_INTERVAL) {
                return true;
            }
        }

        return false;
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context, $payload): void
    {
        $errorMessage = 'This is not a valid date.';

        try {
            if (!empty($this->travelStartDate) &&
                \DateTime::createFromFormat(self::DATE_FORMAT, $this->travelStartDate) === false) {
                $context->buildViolation($errorMessage)
                    ->atPath('travelStartDate')
                    ->addViolation();
            }

            if (!empty($this->clientBirthDate) &&
                \DateTime::createFromFormat(self::DATE_FORMAT, $this->clientBirthDate) === false) {
                $context->buildViolation($errorMessage)
                    ->atPath('clientBirthDate')
                    ->addViolation();
            }

            if (!empty($this->paymentDate) &&
                \DateTime::createFromFormat(self::DATE_FORMAT, $this->paymentDate) === false) {
                $context->buildViolation($errorMessage)
                    ->atPath('paymentDate')
                    ->addViolation();
            }
        } catch (\Exception $e) {
            throw new ValidationFailedException($errorMessage);
        }
    }
}