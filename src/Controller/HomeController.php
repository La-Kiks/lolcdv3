<?php

namespace App\Controller;

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
        private HttpClientInterface $client,
    )
    {
    }

    function extractJsonFilenames(string $html): array
    {
        $pattern = '/<a[^>]*href=["\']([^"\']*\.json)["\'][^>]*>/i';
        preg_match_all($pattern, $html, $matches);
        return $matches[1];
    }

    function validateIdAndNameAndAlias(array $data): bool
    {
        $isIdValid = isset($data['id']) && is_int($data['id']);
        $isNameValid = isset($data['name']) && is_string($data['name']) && trim($data['name']) !== '';
        $isAliasValid = isset($data['alias']) && is_string($data['alias']) && trim($data['alias']) !== '';

        return $isIdValid && $isNameValid && $isAliasValid;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $URL_CHAMPIONS_ID_LIST = "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/" ;

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPIONS_ID_LIST
            );

            if($response->getStatusCode() === 200) {
                $content = $response->getContent();
                $list = $this->extractJsonFilenames($content);

                foreach ($list as $item) {
                    $format = '%s%s';
                    $newUrl = sprintf($format, $URL_CHAMPIONS_ID_LIST, $item);


                    $testURL = 'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/893.json';
                    try {
                        $responseJson = $this->client->request(
                          'GET',
                          // $newUrl
                            $testURL
                        );
                        $contentJson = $responseJson->toArray();

                        if($this->validateIdAndNameAndAlias($contentJson)){
                            $testString = $contentJson['id'] . $contentJson['name'] . $contentJson['alias'];
                            dd($testString);
                        } else {
                            $i = 'not valid';
                        }
                        dd($i);
                    }catch (\Exception){
                        // JSON Url failed
                    }

                }
            }


        } catch(\Exception) {
            // Champions list ULR failed
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
