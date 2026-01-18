<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LearnController extends AbstractController
{
    private function requireLogin(): ?Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_register');
        }
        return null;
    }

    #[Route('/learn', name: 'app_learn', methods: ['GET'])]
    public function index(): Response
    {
        // Learn in navbar -> redirect to your prettier dashboard
        if ($r = $this->requireLogin()) return $r;

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/learn/linux', name: 'app_learn_linux', methods: ['GET'])]
    public function linux(): Response
    {
        if ($r = $this->requireLogin()) return $r;
        return $this->render('learn/linux.html.twig');
    }

    #[Route('/learn/networking', name: 'app_learn_networking', methods: ['GET'])]
    public function networking(): Response
    {
        if ($r = $this->requireLogin()) return $r;
        return $this->render('learn/networking.html.twig');
    }

    #[Route('/learn/reverse', name: 'app_learn_reverse', methods: ['GET'])]
    public function reverse(): Response
    {
        if ($r = $this->requireLogin()) return $r;
        return $this->render('learn/reverse.html.twig');
    }

    #[Route('/learn/mobile', name: 'app_learn_mobile', methods: ['GET'])]
    public function mobile(): Response
    {
        if ($r = $this->requireLogin()) return $r;
        return $this->render('learn/mobile.html.twig');
    }

    #[Route('/learn/devsecops', name: 'app_learn_devsecops', methods: ['GET'])]
    public function devsecops(): Response
    {
        if ($r = $this->requireLogin()) return $r;
        return $this->render('learn/devsecops.html.twig');
    }

    #[Route('/learn/ctf', name: 'app_learn_ctf', methods: ['GET'])]
    public function ctf(): Response
    {
        if ($r = $this->requireLogin()) return $r;
        return $this->render('learn/ctf.html.twig');
    }
}
