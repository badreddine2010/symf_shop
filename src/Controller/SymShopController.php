<?php

namespace App\Controller;

use App\Service\Cart\CarteService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SymShopController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CarteService $carteService): Response
    {
        return $this->render('sym_shop/index.html.twig', [
            'controller_name' => 'SymShopController',
            // 'nbreItems' =>$cartService->getCartQty()
        ]);
    }
}
