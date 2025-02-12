<?php

namespace App\Repository;

use App\Entity\Patch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Patch>
 */
class PatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Patch::class);
    }

    public function findOneByNumero(string $numero): ?Patch
    {
        return $this->findOneBy(['numero' => $numero]);
    }

    public function findMostRecentEntry(): ?string
    {
        $qb = $this->createQueryBuilder('p')
        ->orderBy('p.created_at', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

        /** @var Patch $qb */
        if($qb){
            return $qb->getNumero();
        } else return null;


    }

    public function save (Patch $patch): void
    {
        $this->getEntityManager()->persist($patch);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

}
