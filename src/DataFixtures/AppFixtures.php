<?php

namespace App\DataFixtures;

use App\Entity\Albums;
use App\Entity\Genre;
use App\Entity\User;
use Faker\Generator;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

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

public function __construct(){
    $this->faker = Factory::create("fr_FR");
}

    public function load(ObjectManager $manager): void
    {
        
    }
}
