<?php

namespace App\Controller;

use App\Entity\Presentation;
use App\Form\PresentationType;
use App\Repository\PresentationRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/backend/presentation")
 */
class PresentationController extends AbstractController
{
    private $presentationRepository;
    private $log;
    private $gestionMedia;
    private $cache;

    public function __construct(PresentationRepository $presentationRepository, GestionMedia $gestionMedia, GestionLog $log, CacheInterface $cache)
    {
        $this->presentationRepository = $presentationRepository;
        $this->log = $log;
        $this->gestionMedia = $gestionMedia;
        $this->cache = $cache;
    }

    /**
     * @Route("/", name="presentation_index", methods={"GET"})
     */
    public function index(PresentationRepository $presentationRepository): Response
    {
        // Creation du log
        $action = $this->getUser()->getUsername()." a affiché la liste des rubriques de Qui sommes-nous?";
        $this->log->addLog($action);

        return $this->render('presentation/index.html.twig', [
            'presentations' => $presentationRepository->findBy([],['rubrique'=>"ASC"]),
        ]);
    }

    /**
     * @Route("/new", name="presentation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $presentation = new Presentation();
        $form = $this->createForm(PresentationType::class, $presentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si la rubrique existe déjà alors echec
            $verif = $this->presentationRepository->findOneBy(['rubrique'=>$presentation->getRubrique()]);
            if ($verif){
                $message = "La rubrique ".$presentation->getRubrique()." existe déjà. Veuillez en créer une autre ou modifier la concerné";
                $this->addFlash('danger', $message);

                return $this->redirectToRoute('presentation_new');
            }

            // Gestion du fichier media
            $mediaFile = $form->get('photo')->getData();

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'photo');

                $presentation->setPhoto($media);
            }

            // Gestion du slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($presentation->getRubrique());
            $presentation->setSlug($slug);

            // Gestion de l'auteur
            $user = $this->getUser()->getUsername();
            $presentation->setCreatedBy($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($presentation);
            $entityManager->flush();

            $message = "L'article relatif à ".$presentation->getRubrique()." a été enregistré avec succès";
            $this->addFlash('success', $message);

            // Creation du log
            $action = $this->getUser()->getUsername()." a enregistré la rubrique ".$presentation->getRubrique()." dans Qui sommes-nous?";
            $this->log->addLog($action);

            // Effacer le cache
            $this->cache->delete($presentation->getRubrique());

            return $this->redirectToRoute('presentation_index');
        }

        //Creation du log
        $action = $this->getUser()->getUsername()." a tenté d'enregistrer une rubrique dans qui sommes-nous";
        $this->log->addLog($action);

        return $this->render('presentation/new.html.twig', [
            'presentation' => $presentation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="presentation_show", methods={"GET"})
     */
    public function show(Presentation $presentation): Response
    {
        return $this->render('presentation/show.html.twig', [
            'presentation' => $presentation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="presentation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Presentation $presentation): Response
    {
        $form = $this->createForm(PresentationType::class, $presentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion du fichier media
            $mediaFile = $form->get('photo')->getData();
            $ancienMedia = $request->get('ancien_media'); //dd($ancienMedia);

            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'photo');

                $presentation->setPhoto($media);
                $this->gestionMedia->removeUpload($ancienMedia, 'photo');
            }

            // Gestion du slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($presentation->getRubrique());
            $presentation->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();

            $message = "L'article relatif à ".$presentation->getRubrique()." a été modifié avec succès";
            $this->addFlash('success', $message);

            // Creation du log
            $action = $this->getUser()->getUsername()." a modifié la rubrique ".$presentation->getRubrique()." dans Qui sommes-nous?";
            $this->log->addLog($action);

            // Effacer le cache
            $this->cache->delete($presentation->getRubrique());

            return $this->redirectToRoute('presentation_index');
        }

        //Creation du log
        $action = $this->getUser()->getUsername()." a tenté de modifier la rubrique".$presentation->getRubrique()."dans qui sommes-nous";
        $this->log->addLog($action);

        return $this->render('presentation/edit.html.twig', [
            'presentation' => $presentation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="presentation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Presentation $presentation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$presentation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $action = $this->getUser()->getUsername()." a suprimé la rubrique ".$presentation->getRubrique()." dans Qui sommes-nous?";
            $rubrique = $presentation->getRubrique();
            $entityManager->remove($presentation);
            $entityManager->flush();

            // Creation du log
            $this->log->addLog($action);

            // Effacer le cache
            $this->cache->delete($rubrique);
        }

        return $this->redirectToRoute('presentation_index');
    }
}
