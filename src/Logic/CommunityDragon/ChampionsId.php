<?php

namespace App\Logic\CommunityDragon;

use App\Entity\Champion;
use App\Repository\ChampionRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ChampionsId
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private ChampionRepository $championRepository,
    )
    {
    }

    public function createChampionsFromScratch():void
    {
        $URL_CHAMPIONS_ID_LIST = "https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champions/" ;

        try {
            $response = $this->client->request(
                'GET',
                $URL_CHAMPIONS_ID_LIST
            );

            if($statusCode = $response->getStatusCode() === 200) {
                $content = $response->getContent();
                $list = $this->extractJsonFilenames($content);

                // TODO : For each element of the list, access the URL, check for ID & NAME build the Icon URL
                // If both are available : create a new Champion with the custom ID & Name & Image url.
                $countTotal = 0 ;
                $countValid = 0;
                $countInvalid = 0;
                foreach ($list as $item){
                    $format = '%s%s';
                    $newUrl = sprintf($format, $URL_CHAMPIONS_ID_LIST, $item);

                    try {
                        $responseJson = $this->client->request(
                            'GET',
                            $newUrl
                        );

                        $contentJson = $responseJson->toArray();
                        $countTotal++;

                        if($this->validateIdAndNameAndAlias($contentJson)){
                            $countValid++;
                            $champion = new Champion();
                            $imageUrl = sprintf('%s%s%s',
                                'https://raw.communitydragon.org/latest/plugins/rcp-be-lol-game-data/global/default/v1/champion-icons/',
                                $contentJson['id'],
                                '.png')
                            ;
                            $champion->setName($contentJson['name'])
                                ->setAlias($contentJson['alias'])
                                ->setCustomId($contentJson['id'])
                                ->setImage($imageUrl)
                            ;

                            $this->championRepository->save($champion);

                        } else {
                            $countInvalid++;
                        }

                    }catch (\Exception $e){
                        // JSON Url failed
                        $this->logger->error('Failed to reach the JSON URL. ' . $e->getMessage()) ;
                    }
                }
                $this->championRepository->flush();

                $this->logger->info('Total : ' . $countTotal);
                $this->logger->info('Valid : ' . $countValid);
                $this->logger->info('Invalid : ' . $countInvalid);
            }

        } catch(\Exception $e) {
            $this->logger->error('Failed to reach the champions IDs URL. ' . $e->getMessage());
        }
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
}
