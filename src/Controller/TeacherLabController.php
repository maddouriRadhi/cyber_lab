<?php

namespace App\Controller;

use App\Entity\Lab;
use App\Entity\User;
use App\Repository\LabEnrollmentRepository;
use App\Repository\LabRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TeacherLabController extends AbstractController
{
    #[Route('/teacher/labs', name: 'app_teacher_labs')]
    #[IsGranted('ROLE_TEACHER')]
    public function myLabs(LabRepository $repo): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) throw $this->createAccessDeniedException();

        $labs = $repo->findBy(['teacher' => $user], ['id' => 'DESC']);

        return $this->render('teacher/labs/index.html.twig', [
            'labs' => $labs,
        ]);
    }

    #[Route('/teacher/labs/{id}/enrollments', name: 'app_teacher_lab_enrollments')]
    #[IsGranted('ROLE_TEACHER')]
    public function enrollments(
        Lab $lab,
        LabEnrollmentRepository $enrollRepo
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) throw $this->createAccessDeniedException();

        if ($lab->getTeacher()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Not your lab.');
        }

        $rows = $enrollRepo->findBy(['lab' => $lab], ['id' => 'DESC']);

        return $this->render('teacher/labs/enrollments.html.twig', [
            'lab' => $lab,
            'rows' => $rows,
        ]);
    }
}
