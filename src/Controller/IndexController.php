<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * Redirects from / to API documentation.
     *
     * @Route("/", name="index")
     */
    public function __invoke(): Response
    {
        return $this->redirect('/api');
    }
}
