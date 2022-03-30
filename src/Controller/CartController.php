<?php

namespace App\Controller;

use App\Service\Cart\CarteService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="app_cart")
     *
     */
    public function index(
       CarteService $cart
    ): Response {

        // dd($panierWithDate);
        return $this->render('cart/index.html.twig', [
            'items' => $cart->getCartDetails(),
            // 'nbreItems' => count($panierWithData),
            'total' => $cart->getCartTotal(),
            
        ]);
    }
    /**
     * @Route("/panier/add/{id}",name="cart_add")
     */
    public function add($id, CarteService $carteService)
    {
        $carteService->add($id);
        return $this->redirectToRoute('app_product_client', [
            'controller_name' => 'CartController',
            // 'nbreItems' =>$cartService->getCartQty()
            // dd(count($panier))
            // 'nbreItems'=>$nbreitems
        ]);
    }
     /**
     * @Route("/panier/addQty/{id}",name="cart_addQty")
     */
    public function addQtyItem($id, CarteService $carteService)
    {
        $carteService->addQtyItem($id);
        return $this->redirectToRoute('app_cart', [
            'controller_name' => 'CartController',
            // 'nbreItems' =>$cartService->getCartQty()
            // dd(count($panier))
            // 'nbreItems'=>$nbreitems
        ]);
    }
    /**
     * @Route("/panier/lessQty/{id}",name="cart_lessQty")
     */
    public function lessQtyItem($id, CarteService $carteService)
    {
        $carteService->lessQtyItem($id);
        return $this->redirectToRoute('app_cart', [
            'controller_name' => 'CartController',
            // 'nbreItems' =>$cartService->getCartQty()
            // dd(count($panier))
            // 'nbreItems'=>$nbreitems
        ]);
    }
     /**
     * @Route("/panier/delete",name="cart_delete")
     */
    public function deleteCart(SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        
            unset($panier);
        
        $session->set('panier', []);

        return $this->redirectToRoute('app_cart');
    }
    /**
     * @Route("/panier/remove/{id}",name="cart_remove")
     */
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_cart');
    }
}
