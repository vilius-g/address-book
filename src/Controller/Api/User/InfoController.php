<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class InfoController extends AbstractController
{
    public function __invoke(Request $request, Security $security)
    {
        return $this->getUser();
    }
}
