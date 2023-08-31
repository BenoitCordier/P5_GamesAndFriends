<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/logout', name: 'security.logout')]
    public function logout()
    {
        //Blank
    }

    #[Route('/signin', name: 'security.signin', methods: ['GET', 'POST'])]
    public function signin(Request $request, EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form = $this->createForm(SigninType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Utilisateur ajouté avec succès ! Vous pouvez vous connecter !'
            );

            return $this->redirectToRoute('security.login');
        }

        return $this->render('/pages/security/signin.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
