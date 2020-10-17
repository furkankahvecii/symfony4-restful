<?php

namespace App\Controller;

use App\Entity\Customer;
// use App\Repository\CustomerRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        $user = new Customer();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, "password"));
        $user->setCreatedAt(new \DateTime('now'));

        $em->persist($user);
        $em->flush();

        return new Response(sprintf('Customer %s successfully created', $user->getUsername()));
    }

    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }
}

