<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Genre;
use App\Entity\Albums;
use App\Entity\Authors;
use App\Entity\Music;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
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

        for ($i=0; $i < 5; $i++) { 
           
            $genre = new Genre();
            $genre->setGenreName($this->faker->firstName())
            ->setStatus(true);
            $manager->persist($genre);
            $manager->flush();
        }

        for ($i=0; $i < 5; $i++) { 
           
            $albums = new Albums();
            $albums->setName($this->faker->firstName())
            ->setDate(new DateTime())
            ->setStatus(true);
            $manager->persist($albums);
            $manager->flush();
        }

        for ($i=0; $i < 5; $i++) { 
           
            $author = new Authors();
            $author->setName($this->faker->firstName())
            ->setStatus(true);
            $manager->persist($author);
            $manager->flush();
        }

        for ($i=0; $i < 5; $i++) { 
           
            $music = new Music();
            $music->setMusicTitle($this->faker->firstName())
            ->setReleased(new DateTime())
            ->setStatus(true);
            $manager->persist($music);
            $manager->flush();
        }
        
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
