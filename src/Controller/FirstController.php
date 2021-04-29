<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex;

class FirstController
{
    /**
     * @Route("/first")
     */
   public function first(){
       return new Response("<h1>Hello world</h1>");
   }
}