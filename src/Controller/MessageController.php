<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\MessageThread;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MessageThreadRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
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
    public function newMessage(Request $request, EntityManagerInterface $manager, int $toId, MailerInterface $mailer): Response
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

            // Send email to the user to notify the reception of a new message
            $email = (new TemplatedEmail())
            ->from(new Address('gamesandfriends@gamesandfriends.com', 'Games & Friends'))
            ->to($toUser->getEmail())
            ->subject('Vous avez reçu un nouveau message !')
            ->htmlTemplate('emails/newMessage.html.twig')
            ->context([
                'user' => $toUser,
            ]);
            $mailer->send($email);

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
    public function newMessageInThread(Request $request, EntityManagerInterface $manager, MessageThreadRepository $repository, int $messageThreadId, MailerInterface $mailer): Response
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

            // Send email to the user to notify the reception of a new message
            $email = (new TemplatedEmail())
            ->from('gamesandfriends@gamesandfriends.com')
            ->to($toUser->getEmail())
            ->subject('Vous avez reçu un nouveau message !')
            ->htmlTemplate('emails/newMessage.html.twig')
            ->context([
                'user' => $toUser,
            ]);
            $mailer->send($email);

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

    /**
     * Redirect the user to the adequat newMesssage methods based on the pre-existance of a messageThread
     *
     * @param User $user
     * @param MessageThreadRepository $repository
     * @return void
     */
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
