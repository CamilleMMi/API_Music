<?php

namespace App\DataFixtures;

use App\Entity\Albums;
use App\Entity\Genre;
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

public function __construct(){
    $this->faker = Factory::create("fr_FR");
}

    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 5; $i++) { 
           
            $albums = new Albums();
            $manager->persist($albums);
            $albums->setName($this->faker->firstName())
            ->setAuthor($this->faker->firstName())
            ->setDate($this->faker->dateTime())
            ->setStatus(true);

            $manager->flush();
        }

        for ($i=0; $i < 5; $i++) { 
           
            $genre = new Genre();
            $manager->persist($genre);
            $genre->setGenreName($this->faker->firstName())
            ->setStatus(true);

            $manager->flush();
        }
    }
}
