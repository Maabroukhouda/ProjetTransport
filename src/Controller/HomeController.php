<?php

namespace App\Controller;

use App\Entity\Voyage;
use App\Form\SearchType;
use App\Repository\VoyageRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\VoyageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


    class HomeController extends AbstractController
{

        /**
         * @Route("/MesVoyage", name="voyage_user")
         */
        public function voyageUser(Request $request,ManagerRegistry $doctrine): Response
        {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user= $this->getUser();

            $voyages =$user->getVoyages();

            $repository = $doctrine->getRepository(Voyage::class);
            return $this->render('mesVoyage.html.twig', [
                'voyages'=>$voyages,

            ]);
        }
        /**
         * @Route("/{id}/Supprimer/", name="supprimer")
         */
        public function Supprimer(Request $request,SluggerInterface $slugger,ManagerRegistry $doctrine,$id)
        {
            $entityManager = $doctrine->getManager();

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $repository = $doctrine->getRepository(Voyage::class);
            $voyage = $repository->find($id);
            $entityManager->remove($voyage);
            $entityManager->flush();
            return $this->redirectToRoute('Home');

        }

        /**
         * @Route("/{id}/modifier/", name="modifier")
         */
        public function Modifier(Request $request,SluggerInterface $slugger,ManagerRegistry $doctrine,$id): Response
        {
            $entityManager = $doctrine->getManager();

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $repository = $doctrine->getRepository(Voyage::class);
            $voyage = $repository->find($id);

            $form = $this->createForm(VoyageType::class,$voyage);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $file = $form->get('image')->getData();
                if ($file != null) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('brochures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $voyage->setDate($form->get('date')->getData());
                $voyage->setDepart($form->get('depart')->getData());
                $voyage->setDestination($form->get('destination')->getData());
                $voyage->setNbplace($form->get('nbplace')->getData());
                $voyage->setPrix($form->get('prix')->getData());
                $voyage->setMoyentransport($form->get('moyentransport')->getData());
                $voyage->setImage($newFilename);
                $entityManager->persist($voyage);
                $entityManager->flush();
                return $this->redirectToRoute('Details', ['id' => $id]);
            }
            return $this->render('crud/modifier.html.twig', [
                'voyage' =>$voyage,
                'form' => $form->createView(),

            ]);
        }
    /**
         * @Route("/{id}/Details/", name="Details")
         */
        public function Details(Request $request,ManagerRegistry $doctrine,$id): Response
        {
            $repository = $doctrine->getRepository(Voyage::class);
            $voyage = $repository->find($id);



            return $this->render('crud/details.html.twig', [
                'voyage'=>$voyage,
            ]);
        }
    /**
     * @Route("/", name="Home")
     */
    public function Home(Request $request,VoyageRepository $voyages_repository,ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Voyage::class);

        $voyages = $repository->findAll();
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
       // if ($form->isSubmitted() && $form->isValid()) {
        $depart = $form->get('depart')->getData();
        $destination= $form->get('destination')->getData();
        $min_prix = $form->get('minPrix')->getData();
        $max_prix = $form->get('maxPrix')->getData();
        $min_nb_place = $form->get('minNbplace')->getData();
        $voyages = $repository->filter($min_prix, $max_prix, $min_nb_place,$depart, $destination );

        return $this->render('home.html.twig', [
                'voyages'=>$voyages,
                'form' => $form->createView(),
            ]);
    }



/**
 * @Route("/new", name="Ajouter")
 */
public function newVoyage(Request $request,SluggerInterface $slugger,ManagerRegistry $doctrine): Response
{
    $entityManager = $doctrine->getManager();

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    $form = $this->createForm(VoyageType::class);
    $form->handleRequest($request);
    $voyage = new Voyage();
    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('image')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('brochures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $voyage->setDate($form->get('date')->getData());
            $voyage->setDepart($form->get('depart')->getData());
            $voyage->setDestination($form->get('destination')->getData());
            $voyage->setNbplace($form->get('nbplace')->getData());
            $voyage->setPrix($form->get('prix')->getData());
            $voyage->setMoyentransport($form->get('moyentransport')->getData());
            $voyage->setImage($newFilename);
            $voyage->setUser($user);
            $entityManager->persist($voyage);
            $entityManager->flush();
            return $this->redirectToRoute('Home');

        }
    }
        return $this->render('crud/new.html.twig', [
        'form' => $form->createView(),

    ]);
}


}
