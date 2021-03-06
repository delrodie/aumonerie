<?php

namespace App\Controller;

use App\Entity\Cardinal;
use App\Form\CardinalType;
use App\Repository\CardinalRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMedia;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/backend/cardinal")
 */
class CardinalController extends AbstractController
{
    private $gestionMedia;
    private $log;
    private $cache;

    public function __construct(GestionMedia $gestionMedia, GestionLog $log, CacheInterface $cache)
    {
        $this->gestionMedia = $gestionMedia;
        $this->log = $log;
        $this->cache = $cache;
    }

    /**
     * @Route("/", name="cardinal_index", methods={"GET"})
     * @param CardinalRepository $cardinalRepository
     * @return Response
     */
    public function index(CardinalRepository $cardinalRepository): Response
    {
        // Creation du log
        $action = $this->getUser()->getUsername()." a affiché la liste des messages du Cardinal";
        $this->log->addLog($action);

        return $this->render('cardinal/index.html.twig', [
            'cardinals' => $cardinalRepository->findBy([],['id'=>"DESC"]),
        ]);
    }

    /**
     * @Route("/new", name="cardinal_new", methods={"GET","POST"})
     * @param Request $request
     * @param CardinalRepository $cardinalRepository
     * @return Response
     */
    public function new(Request $request,CardinalRepository $cardinalRepository): Response
    {
        $cardinal = new Cardinal();
        $form = $this->createForm(CardinalType::class, $cardinal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verification de l'existence du message del'année
            $verif = $cardinalRepository->findOneBy(['annee'=>$cardinal->getAnnee()]);
            if ($verif){
                $this->addFlash('danger', "Le message de l'année à déjà été enregistré");

                return $this->redirectToRoute('cardinal_index');
            }

            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($cardinal->getAnnee()).'-'.$slugify->slugify($cardinal->getTheme());
            $cardinal->setSlug($slug);

            // Gestion des fichiers
            $mediaFile = $form->get('photo')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'photo');

                $cardinal->setPhoto($media);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cardinal);
            $entityManager->flush();

            $this->addFlash('seccess', "Le message du cardinala bien été enregistré");

            // Creation du log
            $action = $this->getUser()->getUsername()." a enregistré le message du Cardinal qui a pour theme ".$cardinal->getTheme();
            $this->log->addLog($action);

            // Effacer le cache
            $this->cache->delete('cardinal');

            return $this->redirectToRoute('cardinal_index');
        }

        // Creation du log
        $action = $this->getUser()->getUsername()." a tenté d'enregistrer un message du Cardinal";
        $this->log->addLog($action);

        return $this->render('cardinal/new.html.twig', [
            'cardinal' => $cardinal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cardinal_show", methods={"GET"})
     */
    public function show(Cardinal $cardinal): Response
    {
        // Creation du log
        $action = $this->getUser()->getUsername()." a affiché le message du Cardinal qui a pour theme ".$cardinal->getTheme();
        $this->log->addLog($action);

        return $this->render('cardinal/show.html.twig', [
            'cardinal' => $cardinal,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cardinal_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cardinal $cardinal): Response
    {
        $form = $this->createForm(CardinalType::class, $cardinal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Creation du slug du parteniaire
            $slugify = new Slugify();
            $slug = $slugify->slugify($cardinal->getAnnee()).'-'.$slugify->slugify($cardinal->getTheme());
            $cardinal->setSlug($slug);

            // Gestion des fichiers
            $mediaFile = $form->get('photo')->getData();

            // Traitement du fichier s'il a été telechargé
            if ($mediaFile){
                $media = $this->gestionMedia->upload($mediaFile, 'photo');

                $cardinal->setPhoto($media);
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Le message a bien été modifié");

            // Creation du log
            $action = $this->getUser()->getUsername()." a modifié le message du Cardinal qui a pour theme ".$cardinal->getTheme();
            $this->log->addLog($action);

            // Effacer le cache
            $this->cache->delete('cardinal');

            return $this->redirectToRoute('cardinal_index');
        }

        // Creation du log
        $action = $this->getUser()->getUsername()." a tenté de modifier le message du Cardinal qui a pour theme ".$cardinal->getTheme();
        $this->log->addLog($action);

        return $this->render('cardinal/edit.html.twig', [
            'cardinal' => $cardinal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cardinal_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Cardinal $cardinal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cardinal->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $action = $this->getUser()->getUsername()." a supprimé le message du Cardinal qui a pour theme ".$cardinal->getTheme();
            $entityManager->remove($cardinal);
            $entityManager->flush();

            // Creation du log
            $this->log->addLog($action);

            // Effacer le cache
            $this->cache->delete('cardinal');
        }

        return $this->redirectToRoute('cardinal_index');
    }
}
