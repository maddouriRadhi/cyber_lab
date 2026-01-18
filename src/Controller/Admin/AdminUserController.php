<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_admin_users', methods: ['GET'])]
    public function index(UserRepository $repo): Response
    {
        $users = $repo->findBy([], ['id' => 'DESC']);

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}/toggle', name: 'app_admin_users_toggle', methods: ['POST'])]
    public function toggle(User $target, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('toggle_user_'.$target->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $me = $this->getUser();
        if ($me instanceof User && $target->getId() === $me->getId()) {
            $this->addFlash('danger', 'You cannot disable your own account.');
            return $this->redirectToRoute('app_admin_users');
        }

        $target->setIsActive(!$target->isActive());
        $em->flush();

        $this->addFlash('success', 'User status updated.');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/users/{id}/role', name: 'app_admin_users_role', methods: ['POST'])]
    public function role(User $target, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('role_user_'.$target->getId(), (string)$request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $role = (string)$request->request->get('role');

        // roles allowed
        $map = [
            'student' => ['ROLE_USER'],
            'teacher' => ['ROLE_TEACHER'],
            'admin'   => ['ROLE_ADMIN'],
        ];

        if (!isset($map[$role])) {
            $this->addFlash('danger', 'Invalid role.');
            return $this->redirectToRoute('app_admin_users');
        }

        // prevent removing own admin role
        $me = $this->getUser();
        if ($me instanceof User && $target->getId() === $me->getId() && $role !== 'admin') {
            $this->addFlash('danger', 'You cannot remove your own admin role.');
            return $this->redirectToRoute('app_admin_users');
        }

        $target->setRoles($map[$role]);
        $em->flush();

        $this->addFlash('success', 'User role updated.');
        return $this->redirectToRoute('app_admin_users');
    }
}
