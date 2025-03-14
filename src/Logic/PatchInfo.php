<?php

namespace App\Logic;

use App\Entity\Patch;
use App\Repository\PatchRepository;
use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PatchInfo
{
    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private PatchRepository $patchRepository,

    )
    {
    }
    public function checkPatch():PatchInfoDTO
    {
        $URL_VERSIONS = "https://ddragon.leagueoflegends.com/api/versions.json";

        try {

            $response = $this->client->request(
                'GET',
                $URL_VERSIONS
            );

            if($response->getStatusCode() === 200) {
                $content = $response->toArray();
                $lastVersion = $content[0];

                if ($lastPatchFromDB = $this->patchRepository->findMostRecentEntry()) {
                    if($lastPatchFromDB){
                        if ($lastPatchFromDB == $lastVersion) {
                            $this->logger->info(sprintf('Patch is up to date : %s .', $lastVersion));
                            return new PatchInfoDTO(toUpdate: false, numero: $lastVersion);
                        } else {
                            $this->logger->info(sprintf(
                                'Online version : %s . Local version : %s .',
                                $lastVersion,
                                $lastPatchFromDB
                            ));
                            return new PatchInfoDTO(toUpdate: true, numero: $lastVersion);
                        }
                    }

                    // If there is no patch in the DB - 1st use, DB reset.
                } else {
                    $this->logger->info(sprintf('No patch in the DB, need to update to : %s .', $lastVersion));
                    return new PatchInfoDTO(toUpdate: true, numero: $lastVersion);
                }
            }

        }catch (\Exception $e){
            // JSON Url failed
            $this->logger->error($e->getMessage()) ;
        }

        throw new Exception("Error fetching patch versions.");
    }

    public function updatePatch():void
    {
        $patchInfoDTO = $this->checkPatch();

        if($patchInfoDTO->toUpdate){
            $newPatch = new Patch();
            $newPatch->setNumero($patchInfoDTO->numero);
            $this->patchRepository->save($newPatch);
            $this->patchRepository->flush();

            $this->logger->info('Patch updated.');
        }

    }
    private function createNewPatch(string $version):void
    {
        $newPatch = new Patch();
        $newPatch->setNumero($version);
        $this->patchRepository->save($newPatch);
        $this->patchRepository->flush();

        $this->logger->info(sprintf('Creating new patch entry : %s .', $version));
    }
}
