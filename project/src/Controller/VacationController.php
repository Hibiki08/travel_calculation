<?php

namespace App\Controller;

use App\Service\VacationCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * class VacationController
 * @package App\Controller
 */
class VacationController extends AbstractController
{
    public function __construct(
        private VacationCalculatorService $vacationCalculatorService
    )
    {}

    #[Route('/vacation/calculate', name: 'api_vacation_calculate', methods: 'POST')]
    public function calculateAction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->vacationCalculatorService->processData($data);
        $result = $this->vacationCalculatorService->calculate();

        return $this->json([
            'result' => $result
        ]);
    }
}