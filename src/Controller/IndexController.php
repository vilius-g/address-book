<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function __invoke(): Response
    {
        return $this->render('index.html.twig', ['entrypoint' => 'http://localhost:8000/api']);
    }
}
