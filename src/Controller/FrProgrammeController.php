<?php

namespace App\Controller;

use App\Repository\ProgrammeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/programme")
 */
class FrProgrammeController extends AbstractController
{
    private $programmeRepository;

    public function __construct(ProgrammeRepository $programmeRepository)
    {
        $this->programmeRepository = $programmeRepository;
    }

    /**
     * @Route("/", name="frontend_programme")
     */
    public function programme()
    {
        return $this->render("frontend/programme.html.twig",[
            'programme' => $this->programmeRepository->findOneBy([],['id'=>'DESC'])
        ]);
    }
}