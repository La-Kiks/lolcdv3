<?php

namespace App\Repository;

use App\Entity\Champion;
use App\Entity\Spell;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Spell>
 */
class SpellRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator,
    )
    {
        parent::__construct($registry, Spell::class);
    }

    public function findOneByName(string $name): ?Spell
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findByChampion(Champion $champion): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.champions', 'c')
            ->where('c = :champion')
            ->setParameter('champion', $champion)
            ->orderBy(
                "CASE
                WHEN s.keyboard = 'q' THEN 1
                WHEN s.keyboard = 'w' THEN 2
                WHEN s.keyboard = 'e' THEN 3
                WHEN s.keyboard = 'r' THEN 4
                ELSE 5
            END"
            )
            ->getQuery()
            ->getResult()
        ;

        return $qb;
    }

    /**
     * Be aware some champions have multi spells per key.
     * @param Champion $champion
     * @param string $key
     * @return Spell
     */
    public function findOneByChampionAndKey(Champion $champion, string $key): Spell
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.champions', 'c')
            ->where('c = :champion')
            ->andWhere('s.keyboard = :key')
            ->setParameter('champion', $champion)
            ->setParameter('key', $key)
            ->getQuery()
            ->getOneOrNullResult()
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

        if ($exists){
            return true;
        } else {
            return false;
        }
    }
    public function save (Spell $spell): void
    {
        $this->getEntityManager()->persist($spell);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function pagination(int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.id, s.name, s.keyboard, s.image, s.cooldowns')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        return $this->paginator->paginate(target: $qb, page: $page, limit: $limit );
    }

    public function delete(Spell $spell): void
    {
        $this->getEntityManager()->remove($spell);
    }
}
