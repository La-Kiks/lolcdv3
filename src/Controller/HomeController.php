<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Logic\CommunityDragon\ChampionsId;
use App\Logic\CommunityDragon\Spells;
use App\Logic\Irregulars\EliseSpider;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    public function __construct(
        private ChampionRepository $championRepository,
        private SpellRepository $spellRepository,
        private Spells $spells,
        private EliseSpider $eliseSpider,
    )
    {
    }



    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Load Spider spells
        // $this->eliseSpider->createEliseSpider();

        $test = $this->spells->aurora();
        dd($test);



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


}
