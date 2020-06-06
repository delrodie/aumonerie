<?php


namespace App\Controller;


use App\Entity\Actualite;
use App\Repository\ActualiteRepository;
use App\Utilities\GestionLog;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class FrActualiteController
 * @Route("/actualite")
 */
class FrActualiteController extends AbstractController
{
    private $log;
    private $cache;
    private $actualiteRepository;
    private $paginator;

    public function __construct(CacheInterface $cache, GestionLog $log, ActualiteRepository $actualiteRepository, PaginatorInterface $paginator)
    {
        $this->log = $log;
        $this->cache = $cache;
        $this->actualiteRepository = $actualiteRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="frontend_actualite_index")
     */
    public function index(Request $request)
    {
        $actualiteListe = $this->actualiteRepository->findBy([],['dateActualite'=>"DESC"]);
        // Pagination du resultat
        $actualites = $this->paginator->paginate(
            $actualiteListe,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('frontend/actualites.html.twig',[
            'actualites' => $actualites
        ]);
    }

    /**
     * @Route("/{slug}", name="frontend_actualite_show", methods={"GET"})
     * @param Actualite $actualite
     */
    public function show(Actualite $actualite) : Response
    {
        $resultat = $this->cache->get($actualite->getSlug(), function (ItemInterface $item) use ($actualite) {
            $item->expiresAfter(86400); // 24h
            return $actualite;
        });

        // Creation du log
        if ($this->getUser()){
            $username = $this->getUser()->getUsername();
        }else{
            $username= "Anonyme";
        }
        $action = $username." a consulté l'actualité ".$actualite->getTitre();
        $this->log->addLog($action);

        return $this->render("frontend/actualite.html.twig",[
            'actualite' => $resultat,
        ]);
    }
}