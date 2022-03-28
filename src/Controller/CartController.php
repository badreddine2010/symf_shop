<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="app_cart")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig');
    }

    /**
     * @Route("/add/{idProd}",name="cart_add")
     */
    public function addProduct(int $idProd, SessionInterface $session)
    {
        //Créer ou récupérer le panier s'il existe
        $panier = $session->get('panier', []); //Si panier n'existe pas on récupère un panier vide
        if (!empty($panier[$idProd])) {
            $panier[$idProd]++;
        } else {
            $panier[$idProd] = 1;
        }
        $session->set('panier', $panier);
        dd($panier);
    }

    /**
     * @Route("/delete/{idProd}",name="cart_del")
     */
    public function delProduct(int $idProd, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$idProd])) {
            unset($panier[$idProd]);
        }
        $session->set('panier', $panier);

        dd($panier);
    }
}
