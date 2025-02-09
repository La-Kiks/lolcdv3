<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\PatchRepository;
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
        SpellRepository $spellRepository,
        PatchRepository $patchRepository,
    ): Response
    {
        $patch = $patchRepository->findMostRecentEntry()->getNumero();
        $searchData = new SearchData();
        $champions = [];

        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($searchData->champions as $champ){

                // $name = $champ['name'];
                $haste = $champ['haste'];

                if(!is_numeric($haste)){
                    $haste = 0;
                }

                // if(is_string($name)){
                    // $champion = $championRepository->findOneByNameOrAlias($name);
                    $champion = $champ['name'];
                    if ($champion){
                        $spells = $spellRepository->findByChampion($champion);
                        $CSHDTO = new ChampionSpellsHasteDTO(
                            champion: $champion, spells: $spells, haste: $haste
                        );
                        $champions[] = $CSHDTO;
                    }

                // }
            }
        }

        return $this->render('home/index.html.twig', [
            'patch' => $patch,
            'form' => $form,
            'champions' => $champions,
        ]);
    }
}
