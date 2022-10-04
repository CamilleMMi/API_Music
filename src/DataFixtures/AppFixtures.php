<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Music;
use Generator as GlobalGenerator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
        
    }
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 3; $i++) {
            $music = new Music();
            $music->setName($this->faker->firstName())
            ->setDuration($this->faker->randomNumber(5))
            ->setActive(true);
            $manager->persist($music);

        }
        // $product = new Product();
        // $manager->persist($product);
        for($x = 0; $x <= 100; $x++){
        
        $user = new User();
        $manager->persist($user);

        $user->setName($this->faker->firstName());
        $user->setLastName($this->faker->lastName());
        $user->setDateStart(new DateTime());
        $user->setActive(true);

        $manager->flush();
        }
    }
}
