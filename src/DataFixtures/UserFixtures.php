<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct (UserPasswordHasherInterface $passwordHasher) 
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $plainPassword = '123456';

        $user1 = new User([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com'
        ]);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, $plainPassword));
        $manager->persist($user1);

        $user2 = new User([
            'first_name' => 'Alex',
            'last_name' => 'Smith',
            'email' => 'alex@smith.com'
        ]);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, $plainPassword));
        $manager->persist($user2);

        $user3 = new User([
            'first_name' => 'Doe',
            'last_name' => 'Williams',
            'email' => 'doe@williams.com'
        ]);
        $user3->setPassword($this->passwordHasher->hashPassword($user3, $plainPassword));
        $manager->persist($user3);

        $manager->flush();
    }
}
