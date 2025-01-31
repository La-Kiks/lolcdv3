<?php

namespace App\Controller;

use App\Form\SearchChampionDTO;
use App\Form\SearchChampionType;
use App\Repository\ChampionRepository;
use App\Repository\SpellRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
                $championOne = $championRepository->findOneByNameOrAlias($championNameOne);
                if($championOne){
                    $spellsOne = $spellRepository->findByChampion($championOne);

                }
            }

//            // TODO
//            dd($spellsOne);

            $spellsTwo = null;
            $championTwo = null;
            $championNameTwo = $data->nameTwo;
            $hasteTwo = $data->hasteTwo;
            if(is_string($championNameTwo)){
                $championTwo = $championRepository->findOneByNameOrAlias($championNameTwo);
                if($championTwo){
                    $spellsTwo = $spellRepository->findByChampion($championTwo);

                }
            }

            $spellsThree = null;
            $championThree = null;
            $championNameThree = $data->nameThree;
            $hasteThree = $data->hasteThree;
            if(is_string($championNameThree)){
                $championThree = $championRepository->findOneByNameOrAlias($championNameThree);
                if($championThree){
                    $spellsThree = $spellRepository->findByChampion($championThree);

                }
            }

            $spellsFour = null;
            $championFour = null;
            $championNameFour = $data->nameFour;
            $hasteFour = $data->hasteFour;
            if(is_string($championNameFour)){
                $championFour = $championRepository->findOneByNameOrAlias($championNameFour);
                if($championFour){
                    $spellsFour = $spellRepository->findByChampion($championFour);

                }
            }

            $spellsFive = null;
            $championFive = null;
            $championNameFive = $data->nameFive;
            $hasteFive = $data->hasteFive;
            if(is_string($championNameFive)){
                $championFive = $championRepository->findOneByNameOrAlias($championNameFive);
                if($championFive){
                    $spellsFive = $spellRepository->findByChampion($championFive);

                }
            }

            $spellsSix = null;
            $championSix = null;
            $championNameSix = $data->nameSix;
            $hasteSix = $data->hasteSix;
            if(is_string($championNameSix)){
                $championSix = $championRepository->findOneByNameOrAlias($championNameSix);
                if($championSix){
                    $spellsSix = $spellRepository->findByChampion($championSix);

                }
            }

            $spellsSeven = null;
            $championSeven = null;
            $championNameSeven = $data->nameSeven;
            $hasteSeven = $data->hasteSeven;
            if(is_string($championNameSeven)){
                $championSeven = $championRepository->findOneByNameOrAlias($championNameSeven);
                if($championSeven){
                    $spellsSeven = $spellRepository->findByChampion($championSeven);

                }
            }

            $spellsEight = null;
            $championEight = null;
            $championNameEight = $data->nameEight;
            $hasteEight = $data->hasteEight;
            if(is_string($championNameEight)){
                $championEight = $championRepository->findOneByNameOrAlias($championNameEight);
                if($championEight){
                    $spellsEight = $spellRepository->findByChampion($championEight);

                }
            }

            $spellsNine = null;
            $championNine = null;
            $championNameNine = $data->nameNine;
            $hasteNine = $data->hasteNine;
            if(is_string($championNameNine)){
                $championNine = $championRepository->findOneByNameOrAlias($championNameNine);
                if($championNine){
                    $spellsNine = $spellRepository->findByChampion($championNine);

                }
            }

            $spellsTen = null;
            $championTen = null;
            $championNameTen = $data->nameTen;
            $hasteTen = $data->hasteTen;
            if(is_string($championNameTen)){
                $championTen = $championRepository->findOneByNameOrAlias($championNameTen);
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
}
