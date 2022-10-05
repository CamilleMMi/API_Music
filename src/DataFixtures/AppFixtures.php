<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Genre;
use App\Entity\Albums;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
/**
 * Faker Generator
 * 
 * @var Generator
 */
private Generator $faker;

/**
 * Hash the password 
 * 
 * @var UserPasswordHasherInterface
 */
private $userPasswordHasher;

public function __construct(UserPasswordHasherInterface $userPasswordHasher){
    $this->userPasswordHasher = $userPasswordHasher;
    $this->faker = Factory::create("fr_FR");
}

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $userUser = new User();
            $password = $this->faker->password(3,6);
            $userUser->setUsername($this->faker->userName() . '@' . $password)
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password));
            $manager->persist($userUser);
            $manager->flush();
        }

        for ($i = 0; $i < 1; $i++) {
            $userAdmin = new User();
            $userAdmin->setUsername("admin")
            ->setRoles(["ADMIN"])
            ->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
            $manager->persist($userAdmin);
            $manager->flush();
        }
    }
}
