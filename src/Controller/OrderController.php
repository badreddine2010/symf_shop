<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\OrderLine;
use App\Service\Cart\CartService;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /*
     * @Route("/", name="order_all")
     */
    public function index(OrderRepository $orderRepo): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepo->findAll(),
        ]); //Je passe à mon twig le repository de mon order comme paramètre
    }

    /*
     * @Route("/add/{user}", name="order_add")
     */
    public function addOrder(
        User $user,
        CartService $cart,
        EntityManagerInterface $em
    ) {
        //On a récupéré l'id de l'utilisateur directement à partir de index.twig

        if ($user) {
            $order = new Order();
            $order->setRefcde('CDE' . 'XXXXX');
            $order->setDate(new \DateTimeImmutable());
            $order->setTotal($cart->getCartTotal()); //On utilise la méthode getCartTotal pour récupérer le total de produits dans le panier
            $order->setCustomer($user);

            $em->persist($order);

            //Création des lignes de commandes
            $cartDetails = $cart->getCartDetails();
            foreach ($cartDetails as $line) {
                $orderLine = new OrderLine();
                $orderLine->setQuantity($line['qty']);
                $orderLine->setProduct($line['produit']);
                $orderLine->setOrderNum($order);

                $totalLine = $line['qty'] * $line['produit']->getPrice();

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
