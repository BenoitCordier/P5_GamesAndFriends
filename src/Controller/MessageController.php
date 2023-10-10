<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\MessageThread;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MessageThreadRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * Send a new message from a user to another without an existing message thread between them
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param integer $toId
     * @return Response
     */
    #[Route('/message/newMessage/{fromId}/{toId}', name: 'message.newMessage', methods: ['GET', 'POST'])]
    public function newMessage(Request $request, EntityManagerInterface $manager, int $toId): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $messageThread = new MessageThread();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $toUser = $manager->getRepository(User::class)->find($toId);

            $messageThread->setFirstUser($this->getUser());
            $messageThread->setSecondUser($toUser);
            $manager->persist($messageThread);

            $message->setMessageFrom($this->getUser());
            $message->setMessageTo($toUser);
            $message->setMessageThread($messageThread);
            $messageThread->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($message);
            $manager->flush();

            $this->addFlash(
                'success',
                'Message envoyé avec succès !'
            );

            return $this->redirectToRoute('message.newMessageInThread', ['messageThreadId' => $messageThread->getId()]);
        }

        return $this->render('pages/message/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Send a new message from a user to another with an existing message thread between them
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param integer $toId
     * @return Response
     */
    #[Route('/message/newMessageInThread/{messageThreadId}', name: 'message.newMessageInThread', methods: ['GET', 'POST'])]
    public function newMessageInThread(Request $request, EntityManagerInterface $manager, MessageThreadRepository $repository, int $messageThreadId): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $messageThread = $repository->find($messageThreadId);
        $messages = $messageThread->getMessage();
        if ($messageThread->getFirstUser() === $this->getUser()) {
            $fromUser = $messageThread->getFirstUser();
            $toUser = $messageThread->getSecondUser();
        } else {
            $fromUser = $messageThread->getSecondUser();
            $toUser = $messageThread->getFirstUser();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $message->setMessageFrom($fromUser);
            $message->setMessageTo($toUser);
            $message->setMessageThread($messageThread);
            $messageThread->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($message);
            $manager->flush();

            $this->addFlash(
                'success',
                'Message envoyé avec succès !'
            );

            return $this->redirectToRoute('message.newMessageInThread', ['messageThreadId' => $messageThread->getId()]);
        }

        return $this->render('pages/message/messageThread.html.twig', [
            'form' => $form->createView(),
            'messageThread' => $messageThread,
            'messages' => $messages
        ]);
    }

    #[Route('/message/redirect/{id}', name: 'message.redirect', methods: ['GET', 'POST'])]
    public function view(User $user, MessageThreadRepository $repository)
    {
        $firstUserId = $this->getUser()->getId();
        $secondUserId = $user->getId();
        $queryBuilder = $repository->createQueryBuilder('c');
        $result = $queryBuilder->select('c')
        ->where('c.firstUser = :firstUser and c.secondUser = :secondUser')
        ->orWhere('c.firstUser = :secondUser and c.secondUser = :firstUser')
        ->setParameters(new ArrayCollection([
            new Parameter('firstUser', $firstUserId),
            new Parameter('secondUser', $secondUserId)
        ]))
        ->getQuery()
        ->getResult();
        $messageThread = $result;

        if (count($messageThread) == 0) {
            return $this->redirectToRoute('message.newMessage', ['fromId' => $firstUserId, 'toId' => $secondUserId]);
        } else {
            return $this->redirectToRoute('message.newMessageInThread', ['messageThreadId' => $messageThread[0]->getId()]);
        }
    }
}
