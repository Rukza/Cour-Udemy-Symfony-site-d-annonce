<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {

        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        
        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !==null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter de la partie admin
     * 
     * @Route("/admin/logout", name="admin_account_logout")
     *
     * @return void
     */
    public function logout(){
        //...
    }


    /**
     * Permet d'afficher le formulaire de modification par l'admin
     * 
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @return response 
     */

    public function edit(Ad $ad, Request $request, ObjectManager $manager){
        $form = $this->createForm(AdType::class,$ad);
        

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
                $manager->persist($ad);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
                );
        }




        return $this->render('admin/ad/edit.html.twig',[
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }




    /**
     * Permet de supprimer une annonce
     *
     * @Route ("/admin/ads/{id}/delete", name="admin_ads_delete")
     * 
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager){
            if (count($ad->getBookings()) >0) {
                $this->addFlash(
                    'warning',
                    "Vous ne pouvez pas supprimer l'annonce {$ad->getTitle()} car elle posséde déjà des reservations!"
                );
            }else{
                $manager->remove($ad);
                $manager->flush();
                
                $this->addflash(
                    'success',
                    "L'annonce {$ad->getTitle()} a bien été supprimée !"
                    
                );
            }

        return $this->redirectToRoute('admin_ads_index');

    }
}
