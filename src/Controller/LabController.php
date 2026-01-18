<?php

namespace App\Controller;

use App\Entity\Lab;
use App\Entity\Solve;
use App\Entity\User;
use App\Repository\LabEnrollmentRepository;
use App\Repository\LabRepository;
use App\Repository\SolveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LabController extends AbstractController
{
    #[Route('/labs', name: 'app_labs')]
    #[IsGranted('ROLE_USER')]
    public function index(
        LabRepository $repo,
        SolveRepository $solveRepo
    ): Response {
        $labs = $repo->findBy(['published' => true], ['id' => 'DESC']);

        $solvedIds = [];
        $user = $this->getUser();
        if ($user instanceof User) {
            $solves = $solveRepo->findBy(['user' => $user]);
            foreach ($solves as $s) {
                if ($s->getLab()) $solvedIds[] = $s->getLab()->getId();
            }
        }

        return $this->render('labs/index.html.twig', [
            'labs' => $labs,
            'solvedIds' => $solvedIds,
        ]);
    }

    #[Route('/labs/{id}', name: 'app_lab_show')]
    #[IsGranted('ROLE_USER')]
    public function show(
        Lab $lab,
        Request $request,
        SolveRepository $solveRepo,
        LabEnrollmentRepository $enrollRepo,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $alreadySolved = $solveRepo->findOneBy(['user' => $user, 'lab' => $lab]) !== null;

        // enroll stats
        $enrollCount = $enrollRepo->count(['lab' => $lab]);

        $isStudentOnly = $this->isGranted('ROLE_USER')
            && !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER');

        $isEnrolled = false;
        if ($isStudentOnly) {
            $isEnrolled = $enrollRepo->findOneBy(['lab' => $lab, 'student' => $user]) !== null;
        }

        // flag submit
        if ($request->isMethod('POST') && !$alreadySolved) {
            $submitted = trim((string) $request->request->get('flag'));

            if ($submitted !== '' && hash_equals($lab->getFlag(), $submitted)) {
                $solve = new Solve();
                $solve->setUser($user);
                $solve->setLab($lab);

                $em->persist($solve);
                $em->flush();

                $this->addFlash('success', '✅ Correct flag! Lab completed.');
                return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);
            }

            $this->addFlash('danger', '❌ Wrong flag. Try again.');
        }

        return $this->render('labs/show.html.twig', [
            'lab' => $lab,
            'alreadySolved' => $alreadySolved,
            'enrollCount' => $enrollCount,
            'isEnrolled' => $isEnrolled,
        ]);
    }
}
