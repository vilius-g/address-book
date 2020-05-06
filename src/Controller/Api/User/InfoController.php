<?php
declare(strict_types=1);

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class InfoController extends AbstractController
{
    /**
     * Returns information about current user.
     *
     * @param Request $request
     * @param Security $security
     * @return object|UserInterface|null
     */
    public function __invoke(Request $request, Security $security): ?UserInterface
    {
        return $this->getUser();
    }
}
