<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Form\ProgrammeType;
use App\Repository\ProgrammeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/programme")
 */
class ProgrammeController extends AbstractController
{
    /**
     * @Route("/", name="programme_index", methods={"GET","POST"})
     */
    public function index(Request $request, ProgrammeRepository $programmeRepository): Response
    {
        $programme = new Programme();
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('programme_index');
        }

        return $this->render('programme/index.html.twig', [
            'programmes' => $programmeRepository->findBy([],['id'=>'DESC']),
            'programme' => $programme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="programme_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $programme = new Programme();
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('programme_index');
        }

        return $this->render('programme/new.html.twig', [
            'programme' => $programme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programme_show", methods={"GET"})
     */
    public function show(Programme $programme): Response
    {
        return $this->render('programme/show.html.twig', [
            'programme' => $programme,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="programme_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Programme $programme, ProgrammeRepository $programmeRepository): Response
    {
        $form = $this->createForm(ProgrammeType::class, $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('programme_index');
        }

        return $this->render('programme/edit.html.twig', [
            'programmes' => $programmeRepository->findBy([],['id'=>'DESC']),
            'programme' => $programme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="programme_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Programme $programme): Response
    {
        if ($this->isCsrfTokenValid('delete'.$programme->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($programme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('programme_index');
    }
}
