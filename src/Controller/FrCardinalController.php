<?php


namespace App\Controller;

use App\Entity\Cardinal;
use App\Repository\CardinalRepository;
use App\Utilities\GestionLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class FrCardinalController
 * @Route("/cardinal")
 */
class FrCardinalController extends AbstractController
{
    private $log;
    private $cardinalRepository;
    private $cache;

    public function __construct(CacheInterface $cache, GestionLog $log, CardinalRepository $cardinalRepository)
    {
        $this->log = $log;
        $this->cardinalRepository = $cardinalRepository;
        $this->cache = $cache;
    }

    /**
     * @Route("/{slug}", name="forntend_cardinal_show", methods={"GET"})
     */
    public function show(Cardinal $cardinal)
    {
        $resultat = $this->cache->get('cardinal', function (ItemInterface $item) use ($cardinal) {
            $item->expiresAfter(86400); // 24h
            return $this->cardinalRepository->findOneBy(['slug'=>$cardinal->getSlug()]);
        });

        return $this->render("frontend/cardinal.html.twig",[
            'cardinal' => $resultat,
        ]);
    }
}