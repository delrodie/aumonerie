<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\CardinalRepository;
use App\Repository\PresentationRepository;
use App\Repository\SliderRepository;
use App\Utilities\GestionLog;
use App\Utilities\GestionMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    private $gestMail;
    private $log;

    public function __construct(GestionMail $gestionMail, GestionLog $log)
    {
        $this->gestMail= $gestionMail;
        $this->log = $log;
    }

    /**
     * @Route("/", name="app_accueil")
     */
    public function index(PresentationRepository $presentationRepository, CardinalRepository $cardinalRepository, SliderRepository $sliderRepository, ActualiteRepository $actualiteRepository)
    {

        return $this->render('accueil/index.html.twig', [
            'cardinal' => $cardinalRepository->findOneBy([],['id'=>"DESC"]),
            'vision' => $presentationRepository->findOneBy(['rubrique'=>"mission"]),
            'actualites' => $actualiteRepository->findBy([],['id'=>'DESC']),
            'sliders' => $sliderRepository->findBy([],['id'=>'DESC'])
        ]);
    }

    /**
     * @Route("/menu", name="app_menu")
     */
    public function menu(PresentationRepository $presentationRepository)
    {
        return $this->render('accueil/menu.html.twig',[
            'presentations'=> $presentationRepository->findAll()
        ]);
    }

    /**
     * @Route("/backend/dashboard", name="app_dashboard")
     */
    public function dashboard(Request $request)
    {
        // Creation du log
        $action = "Anonyme a tenté de se connecter au backoffice";
        $this->log->addLog($action);
        return $this->render("accueil/dashbord.html.twig");
    }
}
