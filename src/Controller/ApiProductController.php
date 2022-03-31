<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class ApiProductController extends AbstractController
{
    /**
     * @Route("/product", name="app_api_product",methods={"GET"})
     */
    public function index(ProductRepository $prodRepo,OrderRepository $orderRepository)
    {
        //  dd($jsonData);

        // $prodsNormalized = $normalizerInterface->normalize($prods,null,['groups'=>'prods:read']);
        // $jsonData=json_encode($prodsNormalized);
        // $jsonData = $serializerInterface->serialize($prods, 'json', ['groups' => 'prods:read']);

        //  dd($prodsNormalized,$jsonData);
        // $response = new Response($jsonData,200,["Content-Type"=>"application/json"]);
        // $response = new JsonResponse($jsonData, 200, [], true);
        //simplification de la reponse 2
        // $prods = $prodRepo->findAll();
        // $response = $this->json($prods, 200, [], ['groups' => 'prods:read']);
        // return $response;

        //simplification de la reponse
        // return $this->json($prodRepo->findAll(), 200, [], ['groups' => 'prods:read']);
        return $this->json($orderRepository->findAll(), 200, [], ['groups' => 'prods:read']);
    }
}
