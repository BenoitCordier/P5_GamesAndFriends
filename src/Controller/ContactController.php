<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

#[IsGranted('ROLE_USER', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
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
            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('bca.cordier@gmail.com')
            ->subject($contact->getTitle())
            ->htmlTemplate('emails/contact.html.twig');
            $mailer->send($email);

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
}
