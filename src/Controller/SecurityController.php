<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('Home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',
            ['email' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function inscription(UserPasswordHasherInterface $passwordHasher,Request $request,EntityManagerInterface $manager): Response
    {
        $register= $this->createForm(RegistrationFormType::class);
        $register->handleRequest($request);
        if ($register->isSubmitted() && $register->isValid()) {
            //$data = $register->getData();//get('agreeTerms')->getData();
            $password = $request->request->get('password');
            $confirme_password = $request->request->get('CofirmePassword');
            if($password != $confirme_password)
            {
                $this->addFlash(
                    'password',
                    'vérifier votre mots de passe '
                );
            }
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setPassword($password);
            $hash= $passwordHasher->hashPassword(
                $user,
                $user->getPassword()            );
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('Home');

        }

        return $this->render('security/register.html.twig', [
            'Register' => $register->createView(),

        ]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
