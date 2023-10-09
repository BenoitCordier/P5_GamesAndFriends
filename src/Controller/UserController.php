<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserGameType;
use App\Form\UserLocationType;
use App\Form\UserPasswordType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Allow a registered user access to his profil
     *
     * @return Response
     */
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

    /**
     * Edit the user userName, firstName, lastName
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/edit/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager): Response
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

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Informations modifiées avec succès !'
            );

            return $this->redirectToRoute('user.index', [
                'id' => $this->getUser()->getId()
            ]);
        }

        return $this->render('/pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the user password
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route('/user/editPassword/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
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

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Mot de passe modifié avec succès !'
                );

                return $this->redirectToRoute('user.index', [
                    'id' => $this->getUser()->getId()
                ]);
            } else {
                $this->addFlash(
                    'warning',
                    'Mot de passe incorrect !'
                );
            }
        }

        return $this->render('pages/user/editPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow user to add new games to their profiles
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/editGame/{id}', name: 'user.edit.game', methods: ['GET', 'POST'])]
    public function editGame(User $user, Request $request, EntityManagerInterface $manager): Response
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

        $form = $this->createForm(UserGameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $games = $form->getData();
            foreach ($games as $game) {
                foreach ($game->getIterator() as $u => $uniqueGame) {
                    $user->addGame($uniqueGame);
                }
            }
            $user->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Jeux modifiées avec succès !'
            );

            return $this->redirectToRoute('user.index', [
                'id' => $this->getUser()->getId()
            ]);
        }

        return $this->render('/pages/user/editGame.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Allow user to remove game from their profile
     *
     * @param User $user
     * @param integer $gameId
     * @param EntityManagerInterface $manager
     * @param GameRepository $gameRepository
     * @return Response
     */
    #[Route('/user/removeGame/{id}/{gameId}', name: 'user.remove.game', methods: ['GET', 'POST'])]
    public function removeGame(User $user, int $gameId, EntityManagerInterface $manager, GameRepository $gameRepository): Response
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

        $game = $gameRepository->find($gameId);
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->removeGame($game);
        $manager->flush();

        $this->addFlash(
            'success',
            'Jeu supprimé avec succès !'
        );

        return $this->redirectToRoute('user.index', [
            'id' => $this->getUser()->getId()
        ]);
    }

    /**
     * Allow a user to edit his location
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/user/editLocation/{id}', name: 'user.edit.location', methods: ['GET', 'POST'])]
    public function editLocation(User $user, Request $request, EntityManagerInterface $manager): Response
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

        $form = $this->createForm(UserLocationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData()['location'];
            $user->setLocation($location);
            $user->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Adresse modifiée avec succès !'
            );

            return $this->redirectToRoute('user.index', [
                'id' => $this->getUser()->getId()
            ]);
        }

        return $this->render('/pages/user/editLocation.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Access the public profile of a given user
     *
     * @param User $user
     * @return Response
     */
    #[Route('/user/viewProfile/{id}', name: 'user.viewProfile', methods: ['GET'])]
    public function viewProfile(User $user): Response
    {
        return $this->render('pages/user/viewProfile.html.twig', [
            'user' => $user
        ]);
    }
}
