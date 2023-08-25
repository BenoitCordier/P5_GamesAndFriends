<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Generator;

class UserFixtures extends Fixture
{
    /**
    * @var Generator
    */
    private FakerGenerator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setUserName($this->faker->userName())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setPassword($this->faker->password())
            ->setEmail($this->faker->email());

            $manager->persist($user);
        }

        $manager->flush();
    }

}
