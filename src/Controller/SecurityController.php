<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Form\SigninType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function signin(Request $request, EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form = $this->createForm(SigninType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);

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
