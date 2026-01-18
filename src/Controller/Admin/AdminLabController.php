<?php

namespace App\Controller\Admin;

use App\Entity\Lab;
use App\Entity\User;
use App\Form\LabAdminType;
use App\Repository\LabRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/labs')]
class AdminLabController extends AbstractController
{
    #[Route('', name: 'app_admin_labs')]
    public function index(LabRepository $repo): Response
    {
        return $this->render('admin/labs/index.html.twig', [
            'labs' => $repo->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_admin_labs_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $lab = new Lab();
        $form = $this->createForm(LabAdminType::class, $lab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ if teacher not set, set to current admin
            $me = $this->getUser();
            if ($me instanceof User && $lab->getTeacher() === null) {
                $lab->setTeacher($me);
            }

            $em->persist($lab);
            $em->flush();

            $this->addFlash('success', 'Lab created ✅');
            return $this->redirectToRoute('app_admin_labs');
        }

        return $this->render('admin/labs/form.html.twig', [
            'title' => 'Create Lab',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_labs_edit')]
    public function edit(Lab $lab, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LabAdminType::class, $lab);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Lab updated ✅');
            return $this->redirectToRoute('app_admin_labs');
        }

        return $this->render('admin/labs/form.html.twig', [
            'title' => 'Edit Lab',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle', name: 'app_admin_labs_toggle', methods: ['POST'])]
    public function toggle(Lab $lab, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('toggle_lab_'.$lab->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $lab->setPublished(!$lab->isPublished());
        $em->flush();

        $this->addFlash('success', 'Publish status updated ✅');
        return $this->redirectToRoute('app_admin_labs');
    }

    #[Route('/{id}/delete', name: 'app_admin_labs_delete', methods: ['POST'])]
    public function delete(Lab $lab, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_lab_'.$lab->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($lab);
        $em->flush();

        $this->addFlash('success', 'Lab deleted 🗑️');
        return $this->redirectToRoute('app_admin_labs');
    }
}
