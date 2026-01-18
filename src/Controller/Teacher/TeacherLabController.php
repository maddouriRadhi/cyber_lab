<?php

namespace App\Controller\Teacher;

use App\Entity\Lab;
use App\Entity\User;
use App\Form\TeacherLabType;
use App\Repository\LabEnrollmentRepository;
use App\Repository\LabRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_TEACHER')]
#[Route('/teacher/labs')]
class TeacherLabController extends AbstractController
{
    private function me(): User
    {
        $u = $this->getUser();
        if (!$u instanceof User) throw $this->createAccessDeniedException();
        return $u;
    }

    #[Route('', name: 'app_teacher_labs')]
    public function index(LabRepository $repo): Response
    {
        $teacher = $this->me();

        $labs = $repo->findBy(
            ['teacher' => $teacher],
            ['id' => 'DESC']
        );

        return $this->render('teacher/labs/index.html.twig', [
            'labs' => $labs,
        ]);
    }

    #[Route('/new', name: 'app_teacher_labs_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $teacher = $this->me();

        $lab = new Lab();
        $lab->setTeacher($teacher); // ✅ owner is current teacher

        $form = $this->createForm(TeacherLabType::class, $lab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lab);
            $em->flush();

            $this->addFlash('success', 'Lab created ✅');
            return $this->redirectToRoute('app_teacher_labs');
        }

        return $this->render('teacher/labs/form.html.twig', [
            'title' => 'Create Lab',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teacher_labs_edit')]
    public function edit(Lab $lab, Request $request, EntityManagerInterface $em): Response
    {
        $teacher = $this->me();
        if ($lab->getTeacher()?->getId() !== $teacher->getId()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TeacherLabType::class, $lab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Lab updated ✅');
            return $this->redirectToRoute('app_teacher_labs');
        }

        return $this->render('teacher/labs/form.html.twig', [
            'title' => 'Edit Lab',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle', name: 'app_teacher_labs_toggle', methods: ['POST'])]
    public function toggle(Lab $lab, Request $request, EntityManagerInterface $em): Response
    {
        $teacher = $this->me();
        if ($lab->getTeacher()?->getId() !== $teacher->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('toggle_lab_'.$lab->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $lab->setPublished(!$lab->isPublished());
        $em->flush();

        $this->addFlash('success', 'Publish status updated ✅');
        return $this->redirectToRoute('app_teacher_labs');
    }

    #[Route('/{id}/delete', name: 'app_teacher_labs_delete', methods: ['POST'])]
    public function delete(Lab $lab, Request $request, EntityManagerInterface $em): Response
    {
        $teacher = $this->me();
        if ($lab->getTeacher()?->getId() !== $teacher->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('delete_lab_'.$lab->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($lab);
        $em->flush();

        $this->addFlash('success', 'Lab deleted 🗑️');
        return $this->redirectToRoute('app_teacher_labs');
    }

    #[Route('/{id}/students', name: 'app_teacher_labs_students')]
    public function students(Lab $lab, LabEnrollmentRepository $enrollRepo): Response
    {
        $teacher = $this->me();
        if ($lab->getTeacher()?->getId() !== $teacher->getId()) {
            throw $this->createAccessDeniedException();
        }

        $enrollments = $enrollRepo->findBy(['lab' => $lab], ['id' => 'DESC']);

        return $this->render('teacher/labs/students.html.twig', [
            'lab' => $lab,
            'enrollments' => $enrollments,
        ]);
    }
}
