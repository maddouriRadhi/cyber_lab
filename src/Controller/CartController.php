<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CartController extends AbstractController
{
    private function isStudentOnly(): bool
    {
        return $this->isGranted('ROLE_USER')
            && !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_TEACHER');
    }

    private function catalogMap(): array
    {
        // SKU => [name, price]
        $map = [];
        foreach (PricingController::catalog() as $p) {
            $map[$p['sku']] = ['name' => $p['name'], 'price' => $p['price']];
        }
        return $map;
    }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function cart(SessionInterface $session): Response
    {
        if (!$this->isStudentOnly()) {
            return $this->redirectToRoute('app_pricing');
        }

        $catalog = $this->catalogMap();
        $cart = $session->get('cart', []); // sku => qty

        $lines = [];
        $total = 0;

        foreach ($cart as $sku => $qty) {
            if (!isset($catalog[$sku])) continue;

            $unit = $catalog[$sku]['price'];
            $lineTotal = $unit * (int)$qty;
            $total += $lineTotal;

            $lines[] = [
                'sku' => $sku,
                'name' => $catalog[$sku]['name'],
                'qty' => (int)$qty,
                'unit' => $unit,
                'lineTotal' => $lineTotal,
            ];
        }

        return $this->render('cart/index.html.twig', [
            'lines' => $lines,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{sku}', name: 'app_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(string $sku, Request $request, SessionInterface $session): RedirectResponse
    {
        if (!$this->isStudentOnly()) {
            $this->addFlash('danger', 'Cart is only available for students.');
            return $this->redirectToRoute('app_pricing');
        }

        if (!$this->isCsrfTokenValid('add_to_cart_' . $sku, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_pricing');
        }

        $catalog = $this->catalogMap();
        if (!isset($catalog[$sku])) {
            $this->addFlash('danger', 'Unknown product.');
            return $this->redirectToRoute('app_pricing');
        }

        $cart = $session->get('cart', []);
        $cart[$sku] = ($cart[$sku] ?? 0) + 1;
        $session->set('cart', $cart);

        $this->addFlash('success', 'Added to cart.');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/qty/{sku}', name: 'app_cart_qty', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function qty(string $sku, Request $request, SessionInterface $session): RedirectResponse
    {
        if (!$this->isStudentOnly()) return $this->redirectToRoute('app_pricing');

        if (!$this->isCsrfTokenValid('qty_cart_' . $sku, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_cart');
        }

        $cart = $session->get('cart', []);
        $q = (int) $request->request->get('qty', 1);
        $q = max(1, min(10, $q));

        if (isset($cart[$sku])) {
            $cart[$sku] = $q;
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{sku}', name: 'app_cart_remove', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function remove(string $sku, Request $request, SessionInterface $session): RedirectResponse
    {
        if (!$this->isStudentOnly()) return $this->redirectToRoute('app_pricing');

        if (!$this->isCsrfTokenValid('remove_from_cart_' . $sku, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_cart');
        }

        $cart = $session->get('cart', []);
        unset($cart[$sku]);
        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function clear(Request $request, SessionInterface $session): RedirectResponse
    {
        if (!$this->isStudentOnly()) return $this->redirectToRoute('app_pricing');

        if (!$this->isCsrfTokenValid('clear_cart', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_cart');
        }

        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }

    // ✅ NEW: GET checkout page (so you can click it)
    #[Route('/cart/checkout', name: 'app_cart_checkout_page', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function checkoutPage(SessionInterface $session): Response
    {
        if (!$this->isStudentOnly()) {
            return $this->redirectToRoute('app_pricing');
        }

        $catalog = $this->catalogMap();
        $cart = $session->get('cart', []);

        if (!$cart) {
            $this->addFlash('danger', 'Cart is empty.');
            return $this->redirectToRoute('app_cart');
        }

        $lines = [];
        $total = 0;

        foreach ($cart as $sku => $qty) {
            if (!isset($catalog[$sku])) continue;

            $unit = $catalog[$sku]['price'];
            $lineTotal = $unit * (int)$qty;
            $total += $lineTotal;

            $lines[] = [
                'sku' => $sku,
                'name' => $catalog[$sku]['name'],
                'qty' => (int)$qty,
                'unit' => $unit,
                'lineTotal' => $lineTotal,
            ];
        }

        if ($total <= 0 || !$lines) {
            $this->addFlash('danger', 'Cart has no valid items.');
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('cart/checkout.html.twig', [
            'lines' => $lines,
            'total' => $total,
        ]);
    }

    // ✅ NEW: POST confirm checkout (creates order)
    #[Route('/cart/checkout/confirm', name: 'app_cart_checkout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkoutConfirm(
        Request $request,
        SessionInterface $session,
        EntityManagerInterface $em
    ): RedirectResponse {
        if (!$this->isStudentOnly()) return $this->redirectToRoute('app_pricing');

        if (!$this->isCsrfTokenValid('checkout_cart', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_cart_checkout_page');
        }

        // Demo payment fields (just to look real)
        $cardName = trim((string) $request->request->get('card_name'));
        $cardNumber = preg_replace('/\s+/', '', (string) $request->request->get('card_number'));
        $exp = trim((string) $request->request->get('card_exp'));
        $cvc = trim((string) $request->request->get('card_cvc'));

        if ($cardName === '' || strlen($cardNumber) < 12 || strlen($cardNumber) > 19 || strlen($cvc) < 3 || strlen($cvc) > 4) {
            $this->addFlash('danger', 'Payment info looks invalid (demo).');
            return $this->redirectToRoute('app_cart_checkout_page');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $catalog = $this->catalogMap();
        $cart = $session->get('cart', []);

        if (!$cart) {
            $this->addFlash('danger', 'Cart is empty.');
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setUser($user);
        $order->setStatus('PAID'); // demo

        $total = 0;

        foreach ($cart as $sku => $qty) {
            if (!isset($catalog[$sku])) continue;

            $item = new OrderItem();
            $item->setSku($sku);
            $item->setName($catalog[$sku]['name']);
            $item->setUnitPriceCents($catalog[$sku]['price']);
            $item->setQty((int)$qty);

            $order->addItem($item);
            $total += $catalog[$sku]['price'] * (int)$qty;
        }

        if ($total <= 0 || $order->getItems()->count() === 0) {
            $this->addFlash('danger', 'Cart has no valid items.');
            return $this->redirectToRoute('app_cart');
        }

        $order->setTotalCents($total);

        $em->persist($order);
        $em->flush();

        $session->set('cart', []);

        $this->addFlash('success', '✅ Payment accepted (demo). Order #' . $order->getId());
        return $this->redirectToRoute('app_order_invoice', ['id' => $order->getId()]);
    }
}
