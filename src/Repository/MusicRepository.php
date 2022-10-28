<?php

namespace App\Repository;

use App\Entity\Music;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @extends ServiceEntityRepository<Music>
 *
 * @method Music|null find($id, $lockMode = null, $lockVersion = null)
 * @method Music|null findOneBy(array $criteria, array $orderBy = null)
 * @method Music[]    findAll()
 * @method Music[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Music::class);
    }

    public function save(Music $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Music $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Music[] Returns an array of Music objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Music
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findRandomMusic()
    {
        $qb = $this->createQueryBuilder('m')
        ->where('m.status = 1');

        $qb ->select('count(m.id)');

        $count = $qb->getQuery()->getSingleScalarResult();

        $randomNumber = RAND(0, $count);
        echo($randomNumber);
        $qb1 = $this->createQueryBuilder("m")
            ->select('m')
            ->where('m.status = 1')
            ->setMaxResults(1)
            ->setFirstResult($randomNumber);
        
        return $qb1->getQuery()->getResult();
    }

    public function findWithPagination($page, $limit)
    {
        $qb = $this->createQueryBuilder("m")
        ->where('m.status = 1')
        ->setMaxResults($limit)
        ->setFirstResult(($page - 1) * $limit);

        return $qb->getQuery()->getResult();
    }
}
