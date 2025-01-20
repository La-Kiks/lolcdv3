<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Entity\Spell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Champion>
 */
class ChampionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Champion::class);
    }

    public function findOneByName(string $name): ?Champion
    {
        return $this->findOneBy(['name' => $name]);
    }
    public function findOneByAlias(string $alias): ?Champion
    {
        return $this->findOneBy(['alias' => $alias]);
    }
    public function findOneByCustomId(int $customId): ?Champion
    {
        return $this->findOneBy(['customId' => $customId]);
    }

    public function findOneBySpell(Spell $spell): array
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.spells', 's')
            ->where('s = :spell')
            ->setParameter('spell', $spell)
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }

    /**
     * Check if the champion name already exists in the database.
     *
     * @param string $name The name of the champion.
     * @return bool Return true if the champion name exits, else false.
     */
    public function exists(string $name): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.name')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)
        ;

        $exists = $qb->getQuery()->getOneOrNullResult();

        return $exists !== null;
    }

    public function save(Champion $champion): void
    {
        $this->getEntityManager()->persist($champion);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

}
