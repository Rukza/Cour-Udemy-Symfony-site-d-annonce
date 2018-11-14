<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use \Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @return Reponse
     */
    
    public function create (Request $request, ObjectManager $manager){
        $ad = new Ad();


        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);


        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée"
        );

        if($form->isSubmitted() && $form->isValid()){
            //$manager = $this->getDoctrine()->getManager();

            $manager->persist($ad);
            $manager->flush();

            

            return $this->redirectToRoute('ads_show', [
                'slug'=> $ad->getSlug()
            ]);

        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
        
        
        
    }
    /**
     * Permet d'afficher une seule annonce
     *
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
}
