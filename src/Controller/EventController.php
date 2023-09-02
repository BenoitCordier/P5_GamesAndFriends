<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

            return $this->redirectToRoute('event.index');
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

            return $this->redirectToRoute('event.index');
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
    #[Route('/event/delete/{id}', name: 'event.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Event $event): Response
    {
        $manager->remove($event);
        $manager->flush();

        $this->addFlash(
            'success',
            'Evènement supprimé avec succès !'
        );

        return $this->redirectToRoute('event.index');
    }
}
