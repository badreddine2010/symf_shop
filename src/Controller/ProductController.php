<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="app_product_index")
     */
    public function index(ProductRepository $productRepo): Response
    { 
    //     if (!$this->isGranted('ROLE_ADMIN')) {
    //     return $this->render('/error/accessDenied.html.twig');
    // }
        $products = $productRepo->findAll();
        // dd($products); //dump and die
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
     /**
     * @Route("/productClient", name="productsClient")
     */
    public function indexClient(ProductRepository $productRepo): Response
    {
        
        $products = $productRepo->findAll();
        // dd($products); //dump and die
        return $this->render('product/indexClient.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/product/new",name="prod-new")
     * @Route("/product/edit/{id}",name="prod-edit")
     */
    public function addOrUpdateProduct(
        Product $product = null,
        Request $req,
        EntityManagerInterface $em
    ) {
        if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->render('/error/accessDenied.html.twig');
		}
        if (!$product) {
            $product = new Product();
        }

        $formProduct = $this->createForm(ProductType::class, $product);

        $formProduct->handleRequest($req);
        // dump($req);
        // dump($article);
        if ($formProduct->isSubmitted() && $formProduct->isValid()) {
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('app_product_index', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/prodForm.html.twig', [
            'formProduct' => $formProduct->createView(),
            'mode' => $product->getId() != null,
        ]);
    }
    /**
     * @Route("/product/delete/{id}",name="prod-delete")
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->render('/error/accessDenied.html.twig');
		}
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $entityManager->remove($product);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_pruduct_index', [
            'id' => $product->getId()
        ]);
    }
    
    // /**
    //  * @Route("/product/{id}", name="prod-detail")
    //  */
    // public function show($id,ProductRepository $productRepo): Response
    //    {
    //        $product = $productRepo->find($id);
    //     // dd($article); //dump and die
    //     return $this->render('product/prodDetail.html.twig', [
    //         'product' => $product,
    //     ]);
    // }
}
