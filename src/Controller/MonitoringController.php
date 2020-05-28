<?php

namespace App\Controller;

use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/monitoring")
 */
class MonitoringController extends AbstractController
{
    private $log;

    public function __construct(GestionLog $log)
    {
        $this->log = $log;
    }

    /**
     * @Route("/", name="app_monitoring")
     */
    public function index(Request $request)
    {
        // Si la date est defini par l'utilisateur alors formatter la date puis rechercher
        // Sinon prendre la date du jour
        $search = $request->get('search');

        if ($search) {
            $form = explode('/', $search);
            $date = $form[0].'-'.$form[1].'-'.$form[2];
        }
        else $date = date('Y-m-d');

        // Appel de la fonction d'ouverture du fichier log
        $fichier =$this->log->monitoring($date);

        // Si le fichier n'existe pas alors renvoyer valeur null
        // sinon encoder le fichier en json puis render à la vue
        if (!$fichier) return $this->render('monitoring/index.html.twig',['fichiers'=>null]);
        else{
            krsort($fichier);
            foreach ($fichier as $item => $value){
                $jsons[] = json_decode($value);
            }

            //dd($jsons);


            return $this->render('monitoring/index.html.twig', [
                'fichiers' => $jsons,
            ]);
        }
    }
}
