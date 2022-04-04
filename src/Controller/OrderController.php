<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\OrderLine;
use App\Service\Cart\CartService;
use App\Service\Cart\CarteService;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{

    /**
     *@Route("/", name="order_all") 
     */
    public function index(OrderRepository $orderRepo,UserInterface $user): Response
    {

        return $this->render('order/index.html.twig', [
            'orders' => $orderRepo->findBy(['customer'=>$user]),
            // 'user'=>$orderRepo->getCustomer($user)
        ]); //Je passe à mon twig le repository de mon order comme paramètre
    }
     /**
     *@Route("/all", name="all_order") 
     */
    public function indexAdmin(OrderRepository $orderRepo): Response
    {

        return $this->render('order/index.html.twig', [
            'orders' => $orderRepo->findAll(),
        ]); //Je passe à mon twig le repository de mon order comme paramètre
    }



    /**
     *@Route("/add/{user}", name="order_add") 
     */
    public function addOrder(
        User $user,
        CarteService $cart,
        EntityManagerInterface $em
    ) {
        //On a récupéré l'id de l'utilisateur directement à partir de index.twig
        $faker = \Faker\Factory::create();
        if ($user) {
            $order = new Order();
            $order->setRefcde('Ref' . $faker->numberBetween($min = 1000000, $max = 9999999));
            $order->setDate(new \DateTimeImmutable());
            $order->setTotal($cart->getCartTotal()); //On utilise la méthode getCartTotal pour récupérer le total de produits dans le panier
            $order->setCustomer($user);

            $em->persist($order);

            //Création des lignes de commandes
            $cartDetails = $cart->getCartDetails();
            foreach ($cartDetails as $line) {
                $orderLine = new OrderLine();
                $orderLine->setQuantity($line['quantity']);
                $orderLine->setProduct($line['product']);
                $orderLine->setOrderNum($order);

                $totalLine = $line['quantity'] * $line['product']->getPrice();

                $orderLine->setTotal($totalLine);

                $em->persist($orderLine);
                $em->flush();
            }
            $em->flush();
            $cart->clearCart();
            return $this->redirectToRoute('order_all');
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/detail/{order}", name="order_detail")
     */
    public function showCdeDetail(Order $order)
    {
        return $this->render('/order/orderDetails.html.twig', [
            'ols' => $order->getOrderLines(),
        ]);
    }
}
