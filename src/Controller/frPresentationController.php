<?php


namespace App\Controller;


use App\Entity\Presentation;
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
class frPresentationController extends AbstractController
{
    private $log;
    private $cache;
    private $presentationRepository;

    public function __construct(GestionLog $log, CacheInterface $cache, PresentationRepository $presentationRepository)
    {
        $this->log = $log;
        $this->cache = $cache;
        $this->presentationRepository = $presentationRepository;
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