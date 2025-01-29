<?php

namespace App\Controller;

use App\Form\SearchChampionDTO;
use App\Form\SearchChampionType;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        ChampionRepository $championRepository,
        SpellRepository $spellRepository,
    ): Response
    {
        // TODO : Optimize this DRY
        $data = new SearchChampionDTO();
        $form = $this->createForm(SearchChampionType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $spellsOne = null;
            $championOne = null;
            $championNameOne = $data->nameOne;
            $hasteOne = $data->hasteOne;
            if(is_string($championNameOne)){
                $championOne = $championRepository->findOneByName($championNameOne);
                if($championOne){
                    $spellsOne = $spellRepository->findByChampion($championOne);

                }
            }

            $spellsTwo = null;
            $championTwo = null;
            $championNameTwo = $data->nameTwo;
            $hasteTwo = $data->hasteTwo;
            if(is_string($championNameTwo)){
                $championTwo = $championRepository->findOneByName($championNameTwo);
                if($championTwo){
                    $spellsTwo = $spellRepository->findByChampion($championTwo);

                }
            }

            $spellsThree = null;
            $championThree = null;
            $championNameThree = $data->nameThree;
            $hasteThree = $data->hasteThree;
            if(is_string($championNameThree)){
                $championThree = $championRepository->findOneByName($championNameThree);
                if($championThree){
                    $spellsThree = $spellRepository->findByChampion($championThree);

                }
            }

            $spellsFour = null;
            $championFour = null;
            $championNameFour = $data->nameFour;
            $hasteFour = $data->hasteFour;
            if(is_string($championNameFour)){
                $championFour = $championRepository->findOneByName($championNameFour);
                if($championFour){
                    $spellsFour = $spellRepository->findByChampion($championFour);

                }
            }

            $spellsFive = null;
            $championFive = null;
            $championNameFive = $data->nameFive;
            $hasteFive = $data->hasteFive;
            if(is_string($championNameFive)){
                $championFive = $championRepository->findOneByName($championNameFive);
                if($championFive){
                    $spellsFive = $spellRepository->findByChampion($championFive);

                }
            }

            $spellsSix = null;
            $championSix = null;
            $championNameSix = $data->nameSix;
            $hasteSix = $data->hasteSix;
            if(is_string($championNameSix)){
                $championSix = $championRepository->findOneByName($championNameSix);
                if($championSix){
                    $spellsSix = $spellRepository->findByChampion($championSix);

                }
            }

            $spellsSeven = null;
            $championSeven = null;
            $championNameSeven = $data->nameSeven;
            $hasteSeven = $data->hasteSeven;
            if(is_string($championNameSeven)){
                $championSeven = $championRepository->findOneByName($championNameSeven);
                if($championSeven){
                    $spellsSeven = $spellRepository->findByChampion($championSeven);

                }
            }

            $spellsEight = null;
            $championEight = null;
            $championNameEight = $data->nameEight;
            $hasteEight = $data->hasteEight;
            if(is_string($championNameEight)){
                $championEight = $championRepository->findOneByName($championNameEight);
                if($championEight){
                    $spellsEight = $spellRepository->findByChampion($championEight);

                }
            }

            $spellsNine = null;
            $championNine = null;
            $championNameNine = $data->nameNine;
            $hasteNine = $data->hasteNine;
            if(is_string($championNameNine)){
                $championNine = $championRepository->findOneByName($championNameNine);
                if($championNine){
                    $spellsNine = $spellRepository->findByChampion($championNine);

                }
            }

            $spellsTen = null;
            $championTen = null;
            $championNameTen = $data->nameTen;
            $hasteTen = $data->hasteTen;
            if(is_string($championNameTen)){
                $championTen = $championRepository->findOneByName($championNameTen);
                if($championTen){
                    $spellsTen = $spellRepository->findByChampion($championTen);

                }
            }

            return $this->render('home/index.html.twig', [
                'form' => $form,
                'championOne' => $championOne,
                'spellsOne' => $spellsOne,
                'hasteOne' => $hasteOne,
                'championTwo' => $championTwo,
                'spellsTwo' => $spellsTwo,
                'hasteTwo' => $hasteTwo,
                'championThree' => $championThree,
                'spellsThree' => $spellsThree,
                'hasteThree' => $hasteThree,
                'championFour' => $championFour,
                'spellsFour' => $spellsFour,
                'hasteFour' => $hasteFour,
                'championFive' => $championFive,
                'spellsFive' => $spellsFive,
                'hasteFive' => $hasteFive,
                'championSix' => $championSix,
                'spellsSix' => $spellsSix,
                'hasteSix' => $hasteSix,
                'championSeven' => $championSeven,
                'spellsSeven' => $spellsSeven,
                'hasteSeven' => $hasteSeven,
                'championEight' => $championEight,
                'spellsEight' => $spellsEight,
                'hasteEight' => $hasteEight,
                'championNine' => $championNine,
                'spellsNine' => $spellsNine,
                'hasteNine' => $hasteNine,
                'championTen' => $championTen,
                'spellsTen' => $spellsTen,
                'hasteTen' => $hasteTen,
            ]);
        }
        return $this->render('home/index.html.twig', [
            'form' => $form,
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

    #[Route('/test', name: 'app_test')]
    public function showTest(
        SpellRepository $spellRepository,
        ChampionRepository $championRepository,
        #[MapQueryParameter(options: ['min_range' => 1])]
        int $page = 1,
    ): Response
    {


        return $this->render('base.html.twig', [

        ]);
    }

}
