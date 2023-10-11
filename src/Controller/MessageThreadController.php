<?php

namespace App\Controller;

use App\Repository\MessageThreadRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
class MessageThreadController extends AbstractController
{
    /**
     * Show all the message thread for a user
     *
     * @param MessageThreadRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/message/index', name: 'message.index', methods: ['GET'])]
    public function index(MessageThreadRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $userId = $this->getUser()->getId();

        $queryBuilder = $repository->createQueryBuilder('c');

        $result = $queryBuilder->select('c')
        ->where('c.firstUser = :value or c.secondUser = :value')
        ->setParameter(':value', $userId)
        ->getQuery()
        ->getResult();

        $messageThreads = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/message/index.html.twig', [
            'messageThreads' => $messageThreads
        ]);
    }
}
