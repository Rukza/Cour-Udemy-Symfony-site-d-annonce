<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * permet de se co
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     * 
     * AuthenticationUtils offre utilitaires pour l'authentification
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !==null,
            'username' => $username
        ]);
    }

    /**
     *Permet de se deconnecter
     *
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout(){
        //symfony qui gere
    }
    /**
     * Permet d'afficher le formulaire d'insciption
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){

        $hash = $encoder->encodePassword($user, $user->getHash());
        $user->setHash($hash);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "votre compte a bien été crée!"

            
        );
        return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet d"afficher et de traiter le formulaire de modification de profil
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @return Response
     */
        public function profile(Request $request, ObjectManager $manager){
            $user = $this->getUser();

            $form = $this->createForm(AccountType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                //$manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Les données du pofil ont été enregistrée!"
                );
            }

            return $this->render('account/profile.html.twig',[
                'form' => $form->createView()
            ]);
        }
}
