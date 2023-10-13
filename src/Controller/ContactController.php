<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_USER', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
class ContactController extends AbstractController
{
    /**
     * Allow a user to send a contact form to an admin
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param MailService $mailService
     * @return Response
     */
    #[Route('/contact', name: 'contact.index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $manager, MailService $mailService): Response
    {
        $contact = new Contact();
        $contact->setName($this->getUser()->getName())
        ->setEmail($this->getUser()->getEmail());
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();

            // Send email to the admin to notify the request
            $mailService->sendMail(
                $contact->getEmail(),
                'bca.cordier@gmail.com',
                'Vous avez reçu une nouvelle requête.',
                'emails/contact.html.twig'
            );

            $this->addFlash(
                'success',
                'Votre demande a été envoyée avec succès !'
            );

            return $this->redirectToRoute('user.index', [
                'id' => $this->getUser()->getId()
            ]);
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow a admin to respond to a contact form
     *
     * @param string $email
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/contact/answer/{email}', name: 'contact.answer', methods: ['GET', 'POST'])]
    public function answer(string $email, UserRepository $userRepository): Response
    {
        $user = $userRepository->findBy(['email' => $email]);
        $id = $user[0]->getId();

        return $this->redirectToRoute('message.redirect', ['id' => $id]);
    }
}
