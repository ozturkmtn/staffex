<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    #[Route("/default/{id}", name:"default", methods:["GET"])]
    public function DefaultAction($id, Request $request): Response
    {
        $day = $request->get('day','-');
        
        return new JsonResponse(['123', "id" => $id, "day" => $day, "status" => 200]);
    }
}