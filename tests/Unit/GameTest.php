<?php

namespace App\Tests\Unit;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GameTest extends KernelTestCase
{
    public function getEntity(): Game
    {
        return (new Game())
            ->setGameName('GameName #1');
    }
    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $game = $this->getEntity();
        $errors = $container->get('validator')->validate($game);
        $this->assertCount(0, $errors);
    }

    public function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $game = $this->getEntity();
        $game->setGameName('');
        $errors = $container->get('validator')->validate($game);
        $this->assertCount(2, $errors);
    }
}
