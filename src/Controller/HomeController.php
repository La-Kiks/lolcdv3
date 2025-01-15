<?php

namespace App\Controller;

use App\Logic\CommunityDragon\ChampionsId;
use App\Repository\ChampionRepository;
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
    )
    {
    }



    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
//        $one = $this->championRepository->findAll();
//        dd($one);



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
