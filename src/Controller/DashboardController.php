<?php

namespace App\Controller;

use App\Repository\LabRepository;
use App\Repository\SolveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(
        LabRepository $labRepo,
        SolveRepository $solveRepo
    ): Response {
        // ✅ Admin: redirect to /admin
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // ✅ Teachers now see SAME dashboard as students
        $user = $this->getUser();

        $totalLabs  = $labRepo->count(['published' => true]);
        $latestLabs = $labRepo->findBy(['published' => true], ['createdAt' => 'DESC'], 6);

        $solvedCount = 0;
        if ($user) {
            $solvedCount = $solveRepo->count(['user' => $user]);
        }

        return $this->render('dashboard/index.html.twig', [
            'totalLabs'   => $totalLabs,
            'latestLabs'  => $latestLabs,
            'solvedCount' => $solvedCount,
            'streakDays'  => 0,
            'rank'        => '—',
        ]);
    }
}
