<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search/player/{id}', name: 'search.player', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function searchPlayer(Game $game): JsonResponse
    {
        $users = $game->getUsers();
        $arrayCollection = array();

        foreach($users as $user) {
            $arrayCollection[] = array(
                'name' => $user->getName(),
                'location' => $user->getLocation()
            );
        }

        $response = new JsonResponse($arrayCollection);
        return $response;
    }

    #[Route('/search/event/{id}', name: 'search.event', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function searchEvent(Game $game): JsonResponse
    {
        $events = $game->getEvents();
        $arrayCollection = array();

        foreach ($events as $event) {
            $arrayCollection[] = array(
                'name' => $event->getName(),
                'location' => $event->getLocation()
            );
        }

        $response = new JsonResponse($arrayCollection);
        return $response;
    }

}
