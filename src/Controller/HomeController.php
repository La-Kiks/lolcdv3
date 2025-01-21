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
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Load Spider spells
        // $this->eliseSpider->createEliseSpider();


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/champions', name: 'app_champions')]
    public function showChampions(
        ChampionRepository $championRepository,
        #[MapQueryParameter(options: ['min_range' => 1])]
        int $page = 1,
    ): Response
    {
        $champions = $championRepository->pagination(page: $page, limit: 50);

        return $this->render('home/champions.html.twig', [
            'champions' => $champions,
        ]);
    }

    #[Route('/spells', name: 'app_spells')]
    public function showSpells(
        SpellRepository $spellRepository,
        #[MapQueryParameter(options: ['min_range' => 1])]
        int $page = 1,
    ): Response
    {
        $spells = $spellRepository->pagination(page: $page, limit: 50);

        return $this->render('home/spells.html.twig', [
            'spells' => $spells,
        ]);
    }

}
