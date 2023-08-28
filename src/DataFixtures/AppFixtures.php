<?php

namespace App\DataFixtures;

use Generator;
use Faker\Factory;
use App\Entity\Game;
use App\Entity\User;
use Faker\Generator as FakerGenerator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
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
        // Game
        $games = [];
        for ($i = 1; $i <= 20; $i++) {
            $game = new Game();
            $game->setGameName($this->faker->word());

            $games[] = $game;
            $manager->persist($game);
        }

        // User
        for ($j = 1; $j <= 20; $j++) {
            $user = new User();
            $user->setUserName($this->faker->userName())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setPassword($this->faker->password())
            ->setEmail($this->faker->email());

            for ($k = 1; $k < mt_rand(1, 5); $k++) {
                $user->addGame($games[mt_rand(0, count($games) - 1)]);
            }
            $manager->persist($user);
        }

        $manager->flush();
    }

}
