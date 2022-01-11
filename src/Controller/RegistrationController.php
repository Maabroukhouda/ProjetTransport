<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function inscription(Request $request,EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder, GuardAuthenticatorHandler $guardHandler): Response
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
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('Home');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,          // the User object you just created
                $request,
                $authenticator, // authenticator whose onAuthenticationSuccess you want to use
                'main'          // the name of your firewall in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'Register' => $register->createView(),

        ]);
    }

    /*public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('_profiler_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }*/
}
