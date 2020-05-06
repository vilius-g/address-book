<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Security\Guard\ManualUserAuthenticator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

/**
 * Intercepts when new user is created and logs him in.
 */
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
        if ($this->shouldSkip($event)) {
            return;
        }

        $this->authenticator()->authenticateWithUser($event->getControllerResult(), $event->getRequest(), 'main');
    }

    /**
     * Check if this handler should not be executed for this event.
     *
     * @param ViewEvent $event
     * @return bool
     */
    private function shouldSkip(ViewEvent $event): bool
    {
        return !($event->getControllerResult() instanceof User) ||
            !$event->getRequest()->isMethod(Request::METHOD_POST) ||
            $this->isAuthenticatedPreviously();
    }

    /**
     * Check if user is already authenticated.
     *
     * @return bool TRUE for authenticated user, FALSE for anonymous user
     */
    private function isAuthenticatedPreviously(): bool
    {
        return $this->security()->isGranted('ROLE_USER');
    }

    public function authenticator(): ManualUserAuthenticator
    {
        return $this->container->get(__METHOD__);
    }

    public function security(): Security
    {
        return $this->container->get(__METHOD__);
    }
}
