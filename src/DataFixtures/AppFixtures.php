<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);

        $manager->flush();
    }

    /**
     * Create sample users.
     */
    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test-user@example.com');
        $user->setPassword(
            '$argon2id$v=19$m=65536,t=4,p=1$kwagWtiC+ScCfYuVCZRLuw$Pb7GJ7W8xs98M2L5BEQA9sMrXbq3PsQ7f2GT4MZ+gF8'
        ); // plain value: password123
        $manager->persist($user);
    }
}
