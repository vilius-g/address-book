<?php
declare(strict_types=1);

namespace App\Security\Guard;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Provides a convenient way to manually authenticate user.
 */
class ManualUserAuthenticator
{
    /**
     * @var GuardAuthenticatorHandler
     */
    private $guardAuthenticatorHandler;

    /**
     * ManualUserAuthenticator constructor.
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     */
    public function __construct(GuardAuthenticatorHandler $guardAuthenticatorHandler)
    {
        $this->guardAuthenticatorHandler = $guardAuthenticatorHandler;
    }

    /**
     * Manually authenticate the provided user.
     *
     * @param User $user user instance to authenticate
     * @param Request $request incoming request instance
     * @param string $providerKey firewall name in security.yaml
     */
    public function authenticateWithUser(User $user, Request $request, string $providerKey): void
    {
        $token = $this->createToken($user, $providerKey);
        $this->guardAuthenticatorHandler->authenticateWithToken($token, $request, $providerKey);
    }

    /**
     * Create authentication token from provided user.
     *
     * @param User $user
     * @param string $providerKey
     * @return TokenInterface
     */
    private function createToken(User $user, string $providerKey): TokenInterface
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }
}
