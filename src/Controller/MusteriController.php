<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MusteriController extends AbstractController
{
    /**
    * @Route("/get_musteri", methods={"GET"}, name="getmusteri")
    */
    public function get_musteri()
    {
        return new Response(sprintf('Geldim'));
    }

    /**
    * @Route("/get_musteri_post", methods={"POST"}, name="getmusteripost")
    */
    public function get_musteri_post(Request $request)
    {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        return new Response(var_dump($request));
    }
}
