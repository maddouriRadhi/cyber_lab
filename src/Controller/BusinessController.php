<?php

namespace App\Controller;

use App\Entity\TeacherApplication;
use App\Entity\User;
use App\Form\TeacherApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BusinessController extends AbstractController
{
    #[Route('/business', name: 'app_business')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $application = new TeacherApplication();

        /** @var User|null $user */
        $user = $this->getUser();

        // Prefill fields if user is logged in
        if ($user instanceof User) {
            $application->setEmail($user->getEmail());
            $application->setFullName($user->getFullName());
        }

        $form = $this->createForm(TeacherApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application->setStatus('NEW');

            $em->persist($application);
            $em->flush();

            $this->addFlash('success', '✅ Application sent! We will contact you soon.');
            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/index.html.twig', [
            'form' => $form,
        ]);
    }
}
