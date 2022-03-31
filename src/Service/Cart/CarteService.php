<?php

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use SessionIdInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CarteService
{
    protected $session;
    protected $productRepository;
    public function __construct(
        SessionInterface $session,
        ProductRepository $productRepository
    ) {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function clearCart(){

        $panier = $this->session->get('panier', []);

        if (!empty($panier)) {
            unset($panier);
        }
        $this->session->set('panier', []);    
    }

    public function add(int $id)
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $this->session->set('panier', $panier);
    }
    public function addQtyItem(int $id)
    {
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        }
        $this->session->set('panier', $panier);
    }
    public function lessQtyItem(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id]) && $panier[$id] > 1) {
            $panier[$id]--;
        } else {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    public function getCartDetails(): array
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }
        return $panierWithData;
    }

    public function getCartTotal(): float
    {
        $total = 0;
        foreach ($this->getCartDetails() as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $total;
    }

    public function getCartQty()
    {
        $totalQty = 0;
        $panier = $this->session->get('panier', []);
        foreach ($panier as $id => $qty) {
            $totalQty += $qty;
            # code...
        }
        return $totalQty;
    }
}
