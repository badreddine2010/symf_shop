<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductClientController extends AbstractController
{
    /**
     * @Route("/product/client", name="app_product_client")
     */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product_client/index.html.twig', [
            'controller_name' => 'ProductClientController',
            'products'=>$products
        ]);
    }
}
