<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/actualite")
 */
class ActualiteController extends AbstractController
{
    private $log;
    private $gestionMedia;

    public function __construct(GestionLog $log, GestionMedia $gestionMedia)
    {
        $this->log = $log;
        $this->gestionMedia = $gestionMedia;
    }

    /**
     * @Route("/", name="actualite_index", methods={"GET"})
     */
    public function index(ActualiteRepository $actualiteRepository): Response
    {
        // Creation du log
        $action = $this->getUser()->getUsername()." a affiché la liste des actualités";
        $this->log->addLog($action);

        return $this->render('actualite/index.html.twig', [
            'actualites' => $actualiteRepository->findBy([],['dateActualite'=>'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="actualite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $actualite = new Actualite();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Gestion du fichier media
            $mediaFile = $form->get('media')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'actualite');

                $actualite->setMedia($media);
            }

            // Gestion du slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($actualite->getTitre());
            $actualite->setSlug($slug);

            // Gestion de l'auteur
            $user = $this->getUser()->getUsername();
            $actualite->setCreatedBy($user);

            $entityManager->persist($actualite);
            $entityManager->flush();

            // Creation du log
            $action = $this->getUser()->getUsername()." a enregistré l'article".$actualite->getTitre()." dans actualité";
            $this->log->addLog($action);

            return $this->redirectToRoute('actualite_index');
        }

        // Creation du log
        $action = $this->getUser()->getUsername()." a tenté ds'enregistrer un nouvel article dans actualité";
        $this->log->addLog($action);

        return $this->render('actualite/new.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="actualite_show", methods={"GET"})
     */
    public function show(Actualite $actualite): Response
    {
        // Creation du log
        $action = $this->getUser()->getUsername()." a affiché l'actualité ayant pour titre ".$actualite->getTitre();
        $this->log->addLog($action);

        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="actualite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Actualite $actualite): Response
    {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion du fichier media
            $mediaFile = $form->get('media')->getData();
            $ancienMedia = $request->get('ancien_media');

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'actualite');

                $actualite->setMedia($media);
                $this->gestionMedia->removeUpload($ancienMedia, "actualite");
            }

            $this->getDoctrine()->getManager()->flush();

            // Creation du log
            $action = $this->getUser()->getUsername()." a modifié l'actualité ayant pour titre ".$actualite->getTitre();
            $this->log->addLog($action);

            $this->addFlash('success', "Actualité modifiée avec succès");

            return $this->redirectToRoute('actualite_index');
        }

        // Creation du log
        $action = $this->getUser()->getUsername()." a tenté de modifier l'actualité ayant pour titre ".$actualite->getTitre();
        $this->log->addLog($action);

        return $this->render('actualite/edit.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="actualite_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Actualite $actualite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actualite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $action = $this->getUser()->getUsername()." a suprimé l'actualité qui avait pour titre ".$actualite->getTitre();
            $entityManager->remove($actualite);
            $entityManager->flush();

            // Creation du log
            $this->log->addLog($action);
        }

        return $this->redirectToRoute('actualite_index');
    }
}
