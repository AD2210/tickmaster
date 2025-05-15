<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roles = ['ROLE_USER', 'ROLE_TECHNICIEN', 'ROLE_ADMIN'];
        foreach ($roles as $role) {
            $user = new User();
            $base = strtolower(str_replace('ROLE_', '', $role));
            $user->setFirstname($base);
            $user->setName($base);
            $user->setEmail($base . '@example.com');
            $user->setRole([$role]);
            $user->setPassword($this->hasher->hashPassword($user, 'password123'));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
