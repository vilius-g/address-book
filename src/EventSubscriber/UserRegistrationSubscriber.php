<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Security\LoginApiAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

class UserRegistrationSubscriber implements EventSubscriberInterface, ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['loginUser', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * Automatically authenticate user after creating one.
     *
     * Only works the first time, repeated calls will create new users but will not change authenticated one.
     *
     * @param ViewEvent $event
     */
    public function loginUser(ViewEvent $event): void
    {
        if ($this->security()->isGranted('ROLE_USER')) {
            // Skip this altogether for authenticated users.
            return;
        }

        $user = $event->getControllerResult();
        if (!$user instanceof User || Request::METHOD_POST !== $event->getRequest()->getMethod()) {
            return;
        }

        // Log-in user.
        $this->guardHandler()->authenticateUserAndHandleSuccess(
            $user,
            $event->getRequest(),
            $this->authenticator(),
            'main' // firewall name in security.yaml
        );
    }

    public function guardHandler(): GuardAuthenticatorHandler
    {
        return $this->container->get(__METHOD__);
    }

    public function authenticator(): LoginApiAuthenticator
    {
        return $this->container->get(__METHOD__);
    }

    public function security(): Security
    {
        return $this->container->get(__METHOD__);
    }
}
