<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use \Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
    
        $ads = $repo->findAll();
        

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    
    /**
     * Permet de crée une annonce
     * 
     * @Route("ads/new", name="ads_create")
     * 
     * Permet de donner les droits de créer une annonce seulement au utilisateur co
     * @IsGranted("ROLE_USER")
     * 
     * @return Reponse
     */
    
    public function create (Request $request, ObjectManager $manager){
        $ad = new Ad();

        //$image = new Image();
        //$image->setUrl('http://placehold.it/400x200')
          //    ->setCaption('Titre 1');

        //$ad->addImage($image);

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);


       

        if($form->isSubmitted() && $form->isValid()){
            //$manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('ads_show', [
                'slug'=> $ad->getSlug()
            ]);

        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
        
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * Pose une sécurité plus ciblé avec des propriétés que la version is_granted
     * 
     * @return Response
     */

    public function edit(Ad $ad, Request $request, ObjectManager $manager){
        
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           

            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> on bien été enregistrée"
            );

            return $this->redirectToRoute('ads_show', [
                'slug'=> $ad->getSlug()
            ]);

        }

        return $this->render('ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' =>$ad
        ]);
}


    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show($slug, Ad $ad){
        //je recup l'annonce qui correspond au slug
        //$ad = $repo->findOneBySlug($slug);
    
        return $this->render('ad/show.html.twig',[
            'ad' => $ad
        ]);
    
    }

    /**
     * Permet de supprimer une annonce
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous ne pouvez pas supprimer cette annonce")
     * 
     * @return Response
     * 
     */

    public function delete(Ad $ad, ObjectManager $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"

        );

        return $this->redirectToRoute("ads_index");

    }
}
