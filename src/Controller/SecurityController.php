<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninType;
use App\Repository\UserRepository;
use App\Service\MailService;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Show all user registered in the app
     *
     * @param UserRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted(
        'ROLE_ADMIN',
        message: "Vous n'avez pas l'autorisation d'accéder à cette page."
    )]
    #[Route('/admin/user', name: 'admin.user.index', methods: ['GET'])]
    public function index(UserRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Login method
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Logout method
     *
     * @return void
     */
    #[Route('/logout', name: 'security.logout')]
    public function logout()
    {
        //Blank
    }

    /**
     * Signin method
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/signin', name: 'security.signin', methods: ['GET', 'POST'])]
    public function signin(Request $request, EntityManagerInterface $manager, MailService $mailService): Response
    {
        $user = new User();

        $form = $this->createForm(SigninType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
            $manager->flush();

            // Send email to the user to notify the registration
            $mailService->sendMail(
                'gamesandfriends@gamesandfriends.com',
                $user->getEmail(),
                'Votre inscription sur Games & Friends.',
                'emails/registration.html.twig',
                ['user' => $user]
            );

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
