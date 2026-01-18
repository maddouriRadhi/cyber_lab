<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PricingController extends AbstractController
{
    public static function catalog(): array
    {
        return [
            [
                'sku' => 'starter-linux',
                'name' => 'Linux Starter Pack',
                'desc' => 'Cours + cheatsheet + exercices (démo).',
                'price' => 1500,
                'tag' => 'Beginner',
            ],
            [
                'sku' => 'starter-network',
                'name' => 'Networking Starter Pack',
                'desc' => 'IPv4/IPv6 + subnetting + outils réseau (démo).',
                'price' => 1700,
                'tag' => 'Beginner',
            ],
            [
                'sku' => 'pro-month',
                'name' => 'Pro Subscription (1 month)',
                'desc' => 'Accès à tous les labs + stats + leaderboard.',
                'price' => 1200,
                'tag' => 'Most Popular',
            ],
            [
                'sku' => 'pro-year',
                'name' => 'Pro Subscription (1 year)',
                'desc' => 'Accès Pro pendant 12 mois (économie).',
                'price' => 11500,
                'tag' => 'Save',
            ],
        ];
    }

    #[Route('/pricing', name: 'app_pricing')]
    public function index(): Response
    {
        return $this->render('pricing/index.html.twig', [
            'products' => self::catalog(),
        ]);
    }
}
