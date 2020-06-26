<?php

namespace App\Controller;

use App\Entity\ProgrammeActivite;
use App\Form\ProgrammeActiviteType;
use App\Repository\ProgrammeActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/programme/activite")
 */
class ProgrammeActiviteController extends AbstractController
{
    /**
     * @Route("/", name="programme_activite_index", methods={"GET"})
     */
    public function index(ProgrammeActiviteRepository $programmeActiviteRepository): Response
    {
        return $this->render('programme_activite/index.html.twig', [
            'programme_activites' => $programmeActiviteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{programme}", name="programme_activite_new", methods={"GET","POST"})
     */
    public function new(Request $request, $programme, ProgrammeActiviteRepository $activiteRepository): Response
    {
        $programmeActivite = new ProgrammeActivite();
        $form = $this->createForm(ProgrammeActiviteType::class, $programmeActivite,['programme'=>$programme]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($programmeActivite);
            $entityManager->flush();

            return $this->redirectToRoute('programme_activite_new',['programme'=>$programme]);
        }

        return $this->render('programme_activite/new.html.twig', [
            'programme_activite' => $programmeActivite,
            'activites' => $activiteRepository->findBy(['programme'=>$programme]),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programme_activite_show", methods={"GET"})
     */
    public function show(ProgrammeActivite $programmeActivite): Response
    {
        return $this->render('programme_activite/show.html.twig', [
            'programme_activite' => $programmeActivite,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programme_activite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProgrammeActivite $programmeActivite, ProgrammeActiviteRepository $activiteRepository): Response
    {
        $form = $this->createForm(ProgrammeActiviteType::class, $programmeActivite,['programme'=>$programmeActivite->getProgramme()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programme_activite_new',['programme'=>$programmeActivite->getProgramme()->getId()]);
        }

        return $this->render('programme_activite/edit.html.twig', [
            'programme_activite' => $programmeActivite,
            'activites' => $activiteRepository->findBy(['programme'=>$programmeActivite->getProgramme()]),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programme_activite_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProgrammeActivite $programmeActivite): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programmeActivite->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($programmeActivite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programme_activite_index');
    }
}
