<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
class EventController extends AbstractController
{
    /**
     * Display all the event in the app
     *
     * @param EventRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/event', name: 'event.index', methods: ['GET'])]
    public function index(EventRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $events = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/event/index.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * Display a single event
     *
     * @param Event $event
     * @return Response
     */
    #[Route('/event/viewEvent/{id}', name: 'event.viewEvent', methods: ['GET'])]
    public function viewEvent(Event $event): Response
    {
        return $this->render('pages/event/viewEvent.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * Display the event for which the user is admin
     *
     * @param EventRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/event/myEvent', name: 'event.myevent', methods: ['GET', 'POST'])]
    public function myEvent(EventRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser()->getId();
        $events = $paginator->paginate(
            $repository->findBy(['eventAdmin' => $user]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/event/myEvent.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * Display the event for which the user is registered
     *
     * @param EventRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/event/events', name: 'event.events', methods: ['GET', 'POST'])]
    public function events(EventRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $arrayCollection = array();
        $eventPlayer = $this->getUser();
        $eventsCollection = $repository->findAll();
        foreach ($eventsCollection as $event) {
            $eventPlayers = $event->getEventPlayers();
            if ($eventPlayers->contains($eventPlayer)) {
                $arrayCollection[] = $event;
            }
        }

        $events = $paginator->paginate(
            $arrayCollection,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/event/events.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * Add a new event
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/event/new', name: 'event.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $event->setEventAdmin($this->getUser());

            $manager->persist($event);
            $manager->flush();

            $this->addFlash(
                'success',
                'Evènement ajouté avec succès !'
            );

            return $this->redirectToRoute('event.myevent');
        }

        return $this->render('pages/event/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit an existing event
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted(
        new Expression('is_granted("ROLE_ADMIN") or (is_granted("ROLE_USER") and user === subject.getEventAdmin())'),
        subject: 'event',
        message: "Vous n'avez pas l'autorisation d'accéder à cette page."
    )]
    #[Route('/event/edit/{id}', name: 'event.edit', methods: ['GET', 'POST'])]
    public function edit(Event $event, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();

            $manager->persist($event);
            $manager->flush();

            $this->addFlash(
                'success',
                'Evènement modifié avec succès !'
            );

            return $this->redirectToRoute('event.myevent');
        }

        return $this->render('pages/event/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete an existing event
     *
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted(
        new Expression('is_granted("ROLE_ADMIN") or (is_granted("ROLE_USER") and user === subject.getEventAdmin())'),
        subject: 'event',
        message: "Vous n'avez pas l'autorisation d'accéder à cette page."
    )]
    #[Route('/event/delete/{id}', name: 'event.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Event $event): Response
    {
        $manager->remove($event);
        $manager->flush();

        $this->addFlash(
            'success',
            'Evènement supprimé avec succès !'
        );

        return $this->redirectToRoute('event.myevent');
    }

    /**
     * Allow a user to join an event
     *
     * @param User $user
     * @param integer $eventId
     * @param EntityManagerInterface $manager
     * @param EventRepository $eventRepository
     * @return Response
     */
    #[Route('/event/join/{id}/{eventId}', name: 'event.join', methods: ['GET'])]
    public function joinEvent(User $user, int $eventId, EntityManagerInterface $manager, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($eventId);
        $eventMaxPlayer = $event->getEventMaxPlayer();
        $playerCount = $event->getEventPlayers();
        $playerCount = $playerCount->count();
        $reservationAvalaible = $eventMaxPlayer - $playerCount;

        if ($reservationAvalaible > 0) {
            $event->addEventPlayer($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vous vous êtes bien inscrit !'
            );
        } else {
            $this->addFlash(
                'warning',
                'Il n\'y a plus de place disponible !'
            );
        }
        return $this->redirectToRoute('event.events');
    }

    /**
     * Allow a user to leave an event
     *
     * @param User $user
     * @param integer $eventId
     * @param EntityManagerInterface $manager
     * @param EventRepository $eventRepository
     * @return Response
     */
    #[Route('/event/quit/{id}/{eventId}', name: 'event.quit', methods: ['GET'])]
    public function quitEvent(User $user, int $eventId, EntityManagerInterface $manager, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($eventId);
        $event->removeEventPlayer($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'Vous vous êtes bien désinscrit !'
        );

        return $this->redirectToRoute('event.events');
    }
}
