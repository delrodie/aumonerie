<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use App\Utilities\GestionLog;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @Route("/backend/departement")
 */
class DepartementController extends AbstractController
{
    private $log;
    private $cache;

    public function __construct(GestionLog $log, CacheInterface $cache)
    {
        $this->log = $log;
        $this->cache = $cache;
    }

    /**
     * @Route("/", name="departement_index", methods={"GET"})
     */
    public function index(DepartementRepository $departementRepository): Response
    {
        return $this->render('departement/index.html.twig', [
            'departements' => $departementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="departement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Gestion du slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($departement->getTitre());
            $departement->setSlug($slug);

            $entityManager->persist($departement);
            $entityManager->flush();

            $message = "Le ".$departement->getTitre()." a été enregistré avec succès";
            $this->addFlash('success', $message);

            // Creation du log
            $action = $this->getUser()->getUsername()." a enregistré le ".$departement->getTitre();
            $this->log->addLog($action);

            return $this->redirectToRoute('departement_index');
        }

        return $this->render('departement/new.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="departement_show", methods={"GET"})
     */
    public function show(Departement $departement): Response
    {
        return $this->render('departement/show.html.twig', [
            'departement' => $departement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="departement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Departement $departement): Response
    {
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion du slug
            $slugify = new Slugify();
            $slug = $slugify->slugify($departement->getTitre());
            $departement->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();

            $message = "Le ".$departement->getTitre()." a été modifié avec succès";
            $this->addFlash('success', $message);

            // Creation du log
            $action = $this->getUser()->getUsername()." a modifié le ".$departement->getTitre();
            $this->log->addLog($action);

            return $this->redirectToRoute('departement_index');
        }

        return $this->render('departement/edit.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="departement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Departement $departement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($departement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('departement_index');
    }
}
