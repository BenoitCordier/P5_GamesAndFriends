<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user/user/{id}', name: 'user.index', methods: ['GET'])]
    public function index(User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user) {
            $unauthorisedUserId = $this->getUser()->getId();

            return $this->redirectToRoute('user.index', [
                'id' => $unauthorisedUserId
            ]);
        }

        return $this->render('pages/user/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/edit/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user) {
            $unauthorisedUserId = $this->getUser()->getId();

            return $this->redirectToRoute('user.index', [
                'id' => $unauthorisedUserId
            ]);
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        return $this->render('/pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
