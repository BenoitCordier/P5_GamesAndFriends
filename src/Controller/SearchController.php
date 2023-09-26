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
                'userName' => $user->getUserName(),
                'userLocation' => $user->getUserLocation()
            );
        }

        $response = new JsonResponse($arrayCollection);
        return $response;
    }

    #[Route('/search/event/{id}', name: 'search.event', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function searchEvent(Game $game): JsonResponse
    {
        $events = $game->getEvents();
        $response = new JsonResponse();

        foreach ($events as $event) {
            $response->setData([
                'eventName' => $event->getEventName(),
                'eventLocation' => $event->getEventLocation()
            ]);
            $response->send();
        }
    }

}
