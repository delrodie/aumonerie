<?php


namespace App\Controller;


use App\Entity\Presentation;
use App\Repository\DepartementRepository;
use App\Repository\PresentationRepository;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class frPresentationController
 * @Route("/qui-sommes-nous")
 */
class FrPresentationController extends AbstractController
{
    private $log;
    private $cache;
    private $presentationRepository;
    private $departementRepository;

    public function __construct(GestionLog $log, CacheInterface $cache, PresentationRepository $presentationRepository,DepartementRepository $departementRepository)
    {
        $this->log = $log;
        $this->cache = $cache;
        $this->presentationRepository = $presentationRepository;
        $this->departementRepository = $departementRepository;
    }

    /**
     * @Route("/les-departements", name="frontend_presentation_departements")
     */
    public function departements()
    {
        return $this->render("frontend/departements.html.twig",[
            'departements' => $this->departementRepository->findBy([],['ordre'=>"ASC"]),
        ]);
    }

    /**
     * @Route("/{slug}",name="frontend_presentation_show", methods={"GET"})
     */
    public function show(Presentation $presentation)
    {
        $resultat = $this->cache->get($presentation->getRubrique(), function (ItemInterface $item) use ($presentation) {
            $item->expiresAfter(86400); // 24h
            return $this->presentationRepository->findOneBy(['slug'=>$presentation->getSlug()]);
        });

        // Creation du log
        if ($this->getUser()){
            $username = $this->getUser()->getUsername();
        }else{
            $username= "Anonyme";
        }
        $action = $username." a consulté la rubrique ".$presentation->getRubrique()." de qui sommes-nous?";
        $this->log->addLog($action);

        return $this->render("frontend/presentation.html.twig",[
            'presentation' => $resultat,
        ]);
    }
}