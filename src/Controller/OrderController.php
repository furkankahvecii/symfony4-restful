<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;


date_default_timezone_set("Europe/Istanbul");

class OrderController extends AbstractController
{
    private $entityManager;
    private $jwtEncoder;

    public function __construct(EntityManagerInterface $entityManager, JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->entityManager = $entityManager;
    }

    /**
     * Add Order
     * @Route("/addorder", methods={"POST"}, name="addorder")
     * 
     */
    public function addOrder(Request $request)
    {
        $userid = $this->getUseridFromJWT($request);
        $allParams = $request->request->all();

        $order = new Order();
        $order->setUserid($userid);
        $order->setOrderCode($allParams['orderCode']);
        $order->setProductid($allParams['productId']);
        $order->setQuantity($allParams['quantity']);
        $order->setAddress($allParams['address']);
        $order->setShippingDate(new \DateTime(date('d-m-Y H:i:s',strtotime($allParams['shippingDate']))));

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
  
     /**
     * Update order by orderCode if shippingDate has not arrived
     * @Route("/updateorder", methods={"PUT"}, name="updateorder")
     * 
     */
    public function updateOrder(Request $request)
    {
        $userid = $this->getUseridFromJWT($request);

        $allParams = (array) json_decode($request->getContent());
        $order = $this->getDoctrine()->getRepository(Order::class)->findOneBy(array('userid' => $userid, 'orderCode' => $allParams['orderCode']));
        
        if (!$order) {
            throw $this->createNotFoundException(
                'No order found'
            );
        }

        if(new \DateTime('now') < $order->getShippingDate()) // shippingDate gelmediyse
        {
            // $order->setOrderCode($allParams['orderCode']); // ordercode güncellenemez.
            $order->setProductid(isset($allParams['productId']) ? $allParams['productId'] : $order->getProductId());
            $order->setQuantity(isset($allParams['quantity']) ? $allParams['quantity'] : $order->getQuantity());
            $order->setAddress(isset($allParams['address']) ? $allParams['address'] : $order->getAddress());
            $order->setShippingDate(isset($allParams['shippingDate']) ? new \DateTime(date('d-m-Y H:i:s',strtotime($allParams['shippingDate']))) : $order->getShippingDate());

            $this->entityManager->flush(); //update
            return $order;
        } 

        // shippingDate geçtiyse
        $response = new Response(json_encode(["now_datetime" => date("Y-m-d H:i:s") , "shipping_date" => $order->getShippingDate()->format('Y-m-d H:i:s'), "result" => "shippingdate gelmedi, update yapılamadi."]), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * Order detail by orderCode
     * @Route("/detailorder", methods={"GET"}, name="detailorder")
     * 
     */
    public function detailOrder(Request $request): Response
    {
        $orderCode = $request->query->get('orderCode');
        $userid = $this->getUseridFromJWT($request);
        $order = $this->getDoctrine()->getRepository(Order::class)->findOneBy(array('userid' => $userid, 'orderCode' => $orderCode));

        if (!$order) {
            throw $this->createNotFoundException(
                'No order found'
            );
        }

        $data = array('order' => array());
        $data['order'][] = $this->serializeOrder($order);
      
        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * Lists all Orders by userid
     * @Route("/orders", methods={"GET"}, name="orders")
     * 
     */
    public function getOrders(Request $request)
    {
        $userid = $this->getUseridFromJWT($request);
        $orders = $this->getDoctrine()->getRepository(Order::class)->findBy(array('userid' => $userid));

        if (!$orders) {
            throw $this->createNotFoundException(
                'No orders found'
            );
        }

        $data = array('orders' => array());
     
        foreach ($orders as $order) {
            $data['orders'][] = $this->serializeOrder($order);
        }

        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function serializeOrder(Order $order)
    {
        return array(
            'id' => $order->getId(),
            'userid' => $order->getUserId(),
            'orderCode' => $order->getOrderCode(),
            'productId' => $order->getProductId(),
            'quantity' => $order->getQuantity(),
            'adress' => $order->getAddress(),
            'shippingDate' => $order->getShippingDate(),
        );
    }

    private function getUseridFromJWT(Request $request)
    {
        /*
            Example Token = Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MDI3OTQ2NjksImV4cCI6MTYwMjc5ODI2OSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdF91c2VybmFtZSJ9.Po8bbuPT2hR47r1qw3xfFt-gkSm9nGIeGbR6uVJcNkpJuROd1XJcFQkabIQj6bNZPO8wuye08KEt3GIS88k-U4_JcJqJHwCtfG4vcg2HV8oKXAwcM7lrPHsquffR8j8H0JmXEj7S54L7WvADVM3SIx2z1Bkdn5nHYpjALPpX8VYD-nfSC9lHiyazNlOomCDLcclznrsICAhEWNosbhP2LkSAAInW6sakukIBklkXKYMdN_R42VQzbbzEeptGCxZ0Ji44o0ROKbJCOp4T3Gf_GI7MM2BqqesibJLWaEV8wvkHuUL_mvd83eu5cltqz480dlWQM6MY_uWTkyVQWJ7rvQ
        */

        $token = $request->headers->get('Authorization');
        $token = explode("Bearer ", $token); 
        $username = $this->jwtEncoder->decode($token[1])['username'];
        $userid = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('username' => $username))->getId();
        return $userid;
    }
}
