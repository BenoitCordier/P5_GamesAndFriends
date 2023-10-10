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
        // execute with --purge-exclusions=game
        // User
        $users = [];
        $games = $manager->getRepository(Game::class);
        for ($j = 1; $j <= 20; $j++) {
            $id = mt_rand(1, 11);
            $user = new User();
            $user->setName($this->faker->userName())
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName())
            ->setRoles(['ROLE_USER'])
            ->setEmail($this->faker->email())
            ->setPlainPassword('password')
            ->setLocation($this->faker->address());

            for ($k = 1; $k < mt_rand(1, 5); $k++) {
                $uniqueGame = $games->findOneBy(['id' => $id]);
                $user->addGame($uniqueGame);
            }
            $users[] = $user;
            $manager->persist($user);
        }

        // Event

        for ($l = 0; $l <= 20 ; $l++) {
            $event = new Event();
            $event->setName($this->faker->userName())
            ->setEventGame($games->find(mt_rand(1, 11)))
            ->setEventdescription($this->faker->paragraph())
            ->setEventAdmin($users[mt_rand(0, count($users) - 1)])
            ->setEventMaxPlayer(mt_rand(2, 20))
            ->setLocation($this->faker->address());

            for ($m = 0; $m <= mt_rand(2, 20); $m++) {
                $event->addEventPlayer($users[mt_rand(0, count($users) - 1)]);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }

}
