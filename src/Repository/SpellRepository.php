<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Entity\Spell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Spell>
 */
class SpellRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spell::class);
    }

    public function findOneByName(string $name): Spell
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findByChampion(Champion $champion): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.champions', 'c')
            ->where('c = :champion')
            ->setParameter('champion', $champion)
            ->getQuery()
            ->getResult()
        ;

        return $qb;
    }

    /**
     * Check if the spell name already exists in the database.
     *
     * @param string $name The name of the spell.
     * @return bool Return true if the spell name exits, else false.
     */
    public function exists(string $name): bool
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.name')
            ->where('s.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
        ;

        $exists = $qb->getQuery()->getOneOrNullResult();

        return $exists ?: false;
    }
    public function save (Spell $spell): void
    {
        $this->getEntityManager()->persist($spell);
    }
}
