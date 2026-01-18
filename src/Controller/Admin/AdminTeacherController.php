<?php

namespace App\Controller\Admin;

use App\Entity\TeacherApplication;
use App\Entity\User;
use App\Repository\TeacherApplicationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminTeacherController extends AbstractController
{
    #[Route('/admin/teachers', name: 'app_admin_teachers', methods: ['GET'])]
    public function index(Request $request, TeacherApplicationRepository $repo): Response
    {
        $status = strtoupper(trim((string) $request->query->get('status', 'ALL')));

        $allowed = ['ALL', 'NEW', 'APPROVED', 'REJECTED'];
        if (!in_array($status, $allowed, true)) {
            $status = 'ALL';
        }

        $apps = ($status === 'ALL')
            ? $repo->findBy([], ['createdAt' => 'DESC'])
            : $repo->findBy(['status' => $status], ['createdAt' => 'DESC']);

        return $this->render('admin/teachers/index.html.twig', [
            'apps' => $apps,
            'status' => $status,
            'counts' => [
                'ALL' => $repo->count([]),
                'NEW' => $repo->count(['status' => 'NEW']),
                'APPROVED' => $repo->count(['status' => 'APPROVED']),
                'REJECTED' => $repo->count(['status' => 'REJECTED']),
            ],
        ]);
    }

    #[Route('/admin/teachers/{id}', name: 'app_admin_teachers_show', methods: ['GET'])]
    public function show(TeacherApplication $application): Response
    {
        // ⚠️ IMPORTANT: variable name != "app"
        return $this->render('admin/teachers/show.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/admin/teachers/{id}/approve', name: 'app_admin_teachers_approve', methods: ['POST'])]
    public function approve(
        Request $request,
        TeacherApplication $application,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): RedirectResponse {
        if (!$this->isCsrfTokenValid('teacher_app_approve_' . $application->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_teachers');
        }

        if ($application->getStatus() === 'APPROVED') {
            $this->addFlash('info', 'Already approved.');
            return $this->redirectToRoute('app_admin_teachers');
        }

        $email = mb_strtolower(trim($application->getEmail()));
        $existing = $userRepo->findOneBy(['email' => $email]);

        if ($existing instanceof User) {
            $roles = $existing->getRoles();
            if (!in_array('ROLE_TEACHER', $roles, true)) $roles[] = 'ROLE_TEACHER';
            if (!in_array('ROLE_USER', $roles, true)) $roles[] = 'ROLE_USER';

            $existing->setRoles($roles);
            $existing->setIsActive(true);

            if (trim((string) $existing->getFullName()) === '' && trim($application->getFullName()) !== '') {
                $existing->setFullName($application->getFullName());
            }

        } else {
            $user = new User();
            $user->setEmail($email);
            $user->setFullName($application->getFullName());
            $user->setIsActive(true);
            $user->setRoles(['ROLE_TEACHER', 'ROLE_USER']);

            // Password random affiché à l'admin
            $plain = bin2hex(random_bytes(6)); // 12 chars
            $user->setPassword($hasher->hashPassword($user, $plain));

            $em->persist($user);

            $this->addFlash('info', 'Teacher account created. Temporary password: ' . $plain);
        }

        $application->setStatus('APPROVED');
        $em->flush();

        $this->addFlash('success', 'Teacher application approved.');
        return $this->redirectToRoute('app_admin_teachers');
    }

    #[Route('/admin/teachers/{id}/reject', name: 'app_admin_teachers_reject', methods: ['POST'])]
    public function reject(Request $request, TeacherApplication $application, EntityManagerInterface $em): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('teacher_app_reject_' . $application->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_teachers');
        }

        $application->setStatus('REJECTED');
        $em->flush();

        $this->addFlash('success', 'Teacher application rejected (kept in database).');
        return $this->redirectToRoute('app_admin_teachers');
    }

    #[Route('/admin/teachers/{id}/delete', name: 'app_admin_teachers_delete', methods: ['POST'])]
    public function delete(Request $request, TeacherApplication $application, EntityManagerInterface $em): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('teacher_app_delete_' . $application->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_teachers');
        }

        $em->remove($application);
        $em->flush();

        $this->addFlash('success', 'Teacher application deleted.');
        return $this->redirectToRoute('app_admin_teachers');
    }
}
