<?php

namespace App\Controller;

use App\Entity\User;
// use App\Repository\CustomerRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

date_default_timezone_set("Europe/Istanbul");

class AuthController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $em = $this->getDoctrine()->getManager();

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setCreatedAt(new \DateTime('now'));

        $em->persist($user);
        $em->flush();

        return new Response(json_encode([
            "message" => "User ".$user->getUsername(). " successfully created"
        ]), 200);
    }
}

