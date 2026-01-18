<?php

namespace App\Controller\Admin;

use App\Repository\LabRepository;
use App\Repository\SolveRepository;
use App\Repository\TeacherApplicationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(
        UserRepository $userRepo,
        LabRepository $labRepo,
        TeacherApplicationRepository $teacherAppRepo,
        SolveRepository $solveRepo
    ): Response {
        // --- USERS ---
        $totalUsers = (int) $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()->getSingleScalarResult();

        $activeUsers = (int) $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.isActive = true')
            ->getQuery()->getSingleScalarResult();

        $disabledUsers = (int) $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.isActive = false')
            ->getQuery()->getSingleScalarResult();

        // roles is JSON -> simplest safe count: LIKE
        $teachersCount = (int) $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.roles LIKE :r')
            ->setParameter('r', '%"ROLE_TEACHER"%')
            ->getQuery()->getSingleScalarResult();

        $adminsCount = (int) $userRepo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.roles LIKE :r')
            ->setParameter('r', '%"ROLE_ADMIN"%')
            ->getQuery()->getSingleScalarResult();

        // --- LABS ---
        $totalLabs = (int) $labRepo->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->getQuery()->getSingleScalarResult();

        $publishedLabs = (int) $labRepo->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->andWhere('l.published = true')
            ->getQuery()->getSingleScalarResult();

        $draftLabs = $totalLabs - $publishedLabs;

        // --- TEACHER APPLICATIONS ---
        $applicationsTotal = (int) $teacherAppRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()->getSingleScalarResult();

        $applicationsNew = (int) $teacherAppRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.status = :s')
            ->setParameter('s', 'NEW')
            ->getQuery()->getSingleScalarResult();

        $applicationsApproved = (int) $teacherAppRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.status = :s')
            ->setParameter('s', 'APPROVED')
            ->getQuery()->getSingleScalarResult();

        $applicationsRejected = (int) $teacherAppRepo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.status = :s')
            ->setParameter('s', 'REJECTED')
            ->getQuery()->getSingleScalarResult();

        // --- SOLVES ---
        $totalSolves = (int) $solveRepo->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()->getSingleScalarResult();

        // Latest stuff (optional but cool)
        $latestUsers = $userRepo->findBy([], ['id' => 'DESC'], 5);
        $latestApps  = $teacherAppRepo->findBy([], ['id' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'disabledUsers' => $disabledUsers,
            'teachersCount' => $teachersCount,
            'adminsCount' => $adminsCount,

            'totalLabs' => $totalLabs,
            'publishedLabs' => $publishedLabs,
            'draftLabs' => $draftLabs,

            'applicationsTotal' => $applicationsTotal,
            'applicationsNew' => $applicationsNew,
            'applicationsApproved' => $applicationsApproved,
            'applicationsRejected' => $applicationsRejected,

            'totalSolves' => $totalSolves,

            'latestUsers' => $latestUsers,
            'latestApps' => $latestApps,
        ]);
    }
}
