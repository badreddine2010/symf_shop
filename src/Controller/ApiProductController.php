<?php

namespace App\Controller;
// header("Access-Control-Allow-Origin: *");

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api")
 */
class ApiProductController extends AbstractController
{
    /**
     * @Route("/product", name="app_api_product",methods={"GET"})
     */
    public function index(ProductRepository $prodRepo)
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
        return $this->json($prodRepo->findAll(), 200, [], ['groups' => 'prods:read']);
        // return $this->json($orderRepository->findAll(), 200, [], ['groups' => 'prods:read']);
    }
    /**
     * @Route("/order", name="app_api_order",methods={"GET"})
     *def yourMethod(params):
     *response = flask.jsonify({'some': 'data'})
     *return response
     */
    public function getOrders(OrderRepository $orderRepository)
    {

        return $this->json($orderRepository->findAll(), 200, [], ['groups' => 'orders:read']);
    }

    /**
     * @Route("/product/new",name="api_newProd",methods={"Post"})
     */
    public function addProduct(Request $req, SerializerInterface $serializerInterface, CategoryRepository $catRepo, EntityManagerInterface $em,ValidatorInterface $validator)
    {
        try{

            $jsonData = $req->getContent();
            $prod = $serializerInterface->deserialize($jsonData, Product::class, 'json');
            // dd($prod);
            
            //on a que le nom de la catégorie
            //il nous faut l'id de la catégorie pour initialiser l'attrubut de la catégorie 
            $cat = $prod->getCategory();
            // dd($cat);
            $getCat = $catRepo->findOneBy(['name' => $cat->getName()]);
            // dd($cat, $getCat);
            
            //mettre à jour la catégorie dans le produit
            $prod->setCategory($getCat);
            // dd($prod);
            //avant de persister il faut valider les données
            $errors=$validator->validate($prod);
            if(count($errors)){
                return $this->json($errors,400);
            };            
            //enregistrer le produit ds la basse de donnée
            $em->persist($prod);
            $em->flush($prod);
            
            //envoyer une réponse au client
            return $this->json($prod, 201, [], ['groups' => 'prods:read']);
        }catch(NotEncodableValueException $ex){
            return $this->json(['status_Error'=>400,'message'=>$ex->getMessage()],400);
        }
        }
    }
    