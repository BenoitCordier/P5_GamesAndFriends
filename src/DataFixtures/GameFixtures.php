<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Generator;

class GameFixtures extends Fixture
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
            $game = new Game();
            $game->setGameName($this->faker->word());

            $manager->persist($game);
        }

        $manager->flush();
    }

}
