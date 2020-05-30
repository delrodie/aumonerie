<?php


namespace App\Utilities;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;

class GestionLog
{
    private $logger;
    private $kernel;
    private  $security;
    private $request;

    public function __construct(LoggerInterface $logger, KernelInterface $kernel, Security $security, RequestStack $stack)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->security = $security;
        $this->request = $stack;
    }

    /**
     * @param $user
     * @param $rubrique
     * @param $ip
     * @return bool
     */
    public function addLog($action)
    {
        // Si connecté alors affiché utilisateur
        // sinon affiché anonyme
        if ($this->security->getUser()){
            $username = $this->security->getUser()->getUsername();
        }else{
            $username = "Anonyme";
        }

        $this->logger->info($action,['username'=>$username, 'ip'=>$this->request->getCurrentRequest()->getClientIp()]);

        return true;
    }

    /**
     * Ouverture du fichier log monitoring en fonction de la date etde l'environnement
     *
     * @param $date
     * @return array|bool|false
     */
    public function monitoring($date)
    {
        // Recuperer la date puis affecter l'extension .log a la date
        // recuperer l'environnement encours puis chercher le chemin du repertoire
        $extension = $date.'.log';
        $env = $this->kernel->getEnvironment(); //dd($env);
        $racine = $this->kernel->getProjectDir().'/var/log/'.$env.'.monitoring-'.$extension;

        // Si le fichier n'existe pas alors retourner false
        // Sinon renvoyer le fichier ouvert
        if (!file_exists($racine))return false;
        else{
            $fichier = file($racine);

            return $fichier;
        }

    }

    /**
     * Formattage des actions du log
     *
     * @param $rubrique
     * @return string
     */
    protected function action($rubrique)
    {
        // Formalisation des actions ainsi que les rubriques;
        $dashboard_action = "a affiché le tableau de bord";


        // Affectaion des actions au resultat en fonction de la rubrique
        switch ($rubrique)
        {
            case 'dashboard':
                $result = $dashboard_action;
                break;
            default:
                $result = 'Accueil';
        }

        return $result;
    }
}