<?php

namespace App\Tests\Service;

use App\Service\VacationCalculatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class VacationCalculatorTest
 * @package App\Tests\Service
 */
class VacationCalculatorTest extends TestCase
{
    public function testClientFourteen()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2010';
        $travelStartDate = '10.02.2024';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate
        );
        $this->assertEquals(9000, $vacationCalculatorService->calculate());
    }

    public function testClientThree()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2021';
        $travelStartDate = '10.02.2024';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate
        );
        $this->assertEquals(2000, $vacationCalculatorService->calculate());
    }

    public function testClientTen()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2014';
        $travelStartDate = '10.02.2024';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate
        );
        $this->assertEquals(7000, $vacationCalculatorService->calculate());
    }

    public function testMaxChildDiscount()
    {
        $basePrice = 20000;
        $clientBirthDate = '01.01.2014'; // 10 лет
        $travelStartDate = '10.02.2024';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate
        );
        $this->assertEquals(15500, $vacationCalculatorService->calculate());
    }

    public function testEarlyPlanningSevenPercentage()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2000';
        $travelStartDate = '01.05.2025';
        $paymentDate = '25.10.2024';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate,
            $paymentDate
        );
        $this->assertEquals(9300, $vacationCalculatorService->calculate());
    }

    public function testEarlyPlanningFivePercentage()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2000';
        $travelStartDate = '01.05.2025';
        $paymentDate = '11.12.2024';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate,
            $paymentDate
        );
        $this->assertEquals(9500, $vacationCalculatorService->calculate());
    }

    public function testEarlyPlanningThreePercentage()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2000';
        $travelStartDate = '15.01.2025';
        $paymentDate = '05.10.2025';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate,
            $paymentDate
        );
        $this->assertEquals(9700, $vacationCalculatorService->calculate());
    }

    public function testChildAndEarlyPlanningDiscount()
    {
        $basePrice = 10000;
        $clientBirthDate = '01.01.2014'; // 10 лет
        $travelStartDate = '15.01.2025';
        $paymentDate = '05.10.2025';

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $vacationCalculatorService = new VacationCalculatorService(
            $validator,
            $basePrice,
            $clientBirthDate,
            $travelStartDate,
            $paymentDate
        );
        $this->assertEquals(6790, $vacationCalculatorService->calculate());
    }
}