<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
class SearchController extends AbstractController
{
    /**
     * Search for all players for a specific game
     *
     * @param Game $game
     * @return JsonResponse
     */
    #[Route('/search/player/{id}', name: 'search.player', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function searchPlayer(Game $game): JsonResponse
    {
        $users = $game->getUsers();
        $arrayCollection = array();

        foreach($users as $user) {
            $arrayCollection[] = array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'location' => $user->getLocation()
            );
        }

        $response = new JsonResponse($arrayCollection);
        return $response;
    }

    /**
     * Search for all event for a specific game
     *
     * @param Game $game
     * @return JsonResponse
     */
    #[Route('/search/event/{id}', name: 'search.event', options: ['expose' => true], methods: ['GET', 'POST'])]
    public function searchEvent(Game $game): JsonResponse
    {
        $events = $game->getEvents();
        $arrayCollection = array();

        foreach ($events as $event) {
            $arrayCollection[] = array(
                'id' => $event->getId(),
                'name' => $event->getName(),
                'location' => $event->getLocation()
            );
        }

        $response = new JsonResponse($arrayCollection);
        return $response;
    }

}
