<?php

namespace App\Controller;

use App\Repository\SolveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LeaderboardController extends AbstractController
{
    #[Route('/leaderboard', name: 'app_leaderboard')]
    #[IsGranted('ROLE_USER')]
    public function index(SolveRepository $solveRepo): Response
    {
        $rows = $solveRepo->getLeaderboard();

        return $this->render('leaderboard/index.html.twig', [
            'rows' => $rows,
        ]);
    }
}
