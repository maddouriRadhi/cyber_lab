<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    #[Route('/orders/{id}/invoice', name: 'app_order_invoice', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function invoice(Order $order): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // Only owner OR admin can view invoice
        if (!$this->isGranted('ROLE_ADMIN') && $order->getUser()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('orders/invoice.html.twig', [
            'order' => $order,
        ]);
    }
}
