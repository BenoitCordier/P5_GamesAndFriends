<?php

namespace App\DataFixtures;

use Generator;
use Faker\Factory;
use App\Entity\Game;
use App\Entity\User;
use App\Entity\Event;
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
        $users = [];
        for ($j = 1; $j <= 20; $j++) {
            $user = new User();
            $user->setUserName($this->faker->userName())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setRoles(['ROLE_USER'])
            ->setEmail($this->faker->email())
            ->setPlainPassword('password')
            ->setUserLocation($this->faker->address());

            for ($k = 1; $k < mt_rand(1, 5); $k++) {
                $user->addGame($games[mt_rand(0, count($games) - 1)]);
            }
            $users[] = $user;
            $manager->persist($user);
        }

        // Event

        for ($l = 0; $l <= 20 ; $l++) {
            $event = new Event();
            $event->setEventName($this->faker->userName())
            ->setEventGame($games[mt_rand(0, count($games) - 1)])
            ->setEventdescription($this->faker->paragraph())
            ->setEventAdmin($users[mt_rand(0, count($users) - 1)])
            ->setEventMaxPlayer(mt_rand(2, 20));

            for ($m = 0; $m <= mt_rand(2, 20); $m++) {
                $event->addEventPlayer($users[mt_rand(0, count($users) - 1)]);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }

}
