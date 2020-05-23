<?php

namespace App\Controller;

use App\Entity\Cardinal;
use App\Form\CardinalType;
use App\Repository\CardinalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/cardinal")
 */
class CardinalController extends AbstractController
{
    /**
     * @Route("/", name="cardinal_index", methods={"GET"})
     */
    public function index(CardinalRepository $cardinalRepository): Response
    {
        return $this->render('cardinal/index.html.twig', [
            'cardinals' => $cardinalRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="cardinal_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cardinal = new Cardinal();
        $form = $this->createForm(CardinalType::class, $cardinal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cardinal);
            $entityManager->flush();

            return $this->redirectToRoute('cardinal_index');
        }

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
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cardinal_index');
        }

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
            $entityManager->remove($cardinal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cardinal_index');
    }
}
