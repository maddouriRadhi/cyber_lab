<?php

namespace App\Controller;

use App\Entity\Lab;
use App\Entity\LabEnrollment;
use App\Entity\User;
use App\Repository\LabEnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LabEnrollmentController extends AbstractController
{
    private function isStudentOnly(): bool
    {
        return $this->isGranted('ROLE_USER')
            && !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER');
    }

    #[Route('/labs/{id}/enroll', name: 'app_lab_enroll', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function enroll(
        Lab $lab,
        Request $request,
        LabEnrollmentRepository $repo,
        EntityManagerInterface $em
    ): RedirectResponse {
        if (!$this->isStudentOnly()) return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);

        if (!$this->isCsrfTokenValid('enroll_lab_' . $lab->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if (!$user instanceof User) throw $this->createAccessDeniedException();

        // capacity check
        if ($lab->getMaxSeats() !== null) {
            $count = $repo->count(['lab' => $lab]);
            if ($count >= $lab->getMaxSeats()) {
                $this->addFlash('danger', '❌ No seats available.');
                return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);
            }
        }

        $existing = $repo->findOneBy(['lab' => $lab, 'student' => $user]);
        if ($existing) {
            $this->addFlash('success', 'Already enrolled ✅');
            return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);
        }

        $e = new LabEnrollment();
        $e->setLab($lab);
        $e->setStudent($user);

        $em->persist($e);
        $em->flush();

        $this->addFlash('success', '✅ Enrolled successfully.');
        return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);
    }

    #[Route('/labs/{id}/unenroll', name: 'app_lab_unenroll', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function unenroll(
        Lab $lab,
        Request $request,
        LabEnrollmentRepository $repo,
        EntityManagerInterface $em
    ): RedirectResponse {
        if (!$this->isStudentOnly()) return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);

        if (!$this->isCsrfTokenValid('unenroll_lab_' . $lab->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();
        if (!$user instanceof User) throw $this->createAccessDeniedException();

        $existing = $repo->findOneBy(['lab' => $lab, 'student' => $user]);
        if ($existing) {
            $em->remove($existing);
            $em->flush();
            $this->addFlash('success', 'Unenrolled ✅');
        }

        return $this->redirectToRoute('app_lab_show', ['id' => $lab->getId()]);
    }
}
