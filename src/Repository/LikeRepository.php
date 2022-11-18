<?php

namespace App\Repository;

use App\Entity\Like;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function save(Like $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Like $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Like[] Returns an array of Like objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Like
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findWithPagination($page, $limit)
    {
        $qb = $this->createQueryBuilder("a")
        ->where('a.status = 1')
        ->setMaxResults($limit)
        ->setFirstResult(($page - 1) * $limit);

        return $qb->getQuery()->getResult();
    }

    public function findBetweenDates(DateTimeImmutable $startDate, DateTimeImmutable $endDate, int $page, int $limit) {
        $startDate = $startDate ? $startDate : new DateTimeImmutable();
        $qb = $this->createQueryBuilder("a");
        $qb->add(
            'where',
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gte("a.dateStart", ":startdate"),
                    $qb->expr()->lte("a.dateStart", ":enddate")
                ),
                $qb->expr()->andX(
                    $qb->expr()->gte("a.dateEnd", ":startdate"),
                    $qb->expr()->lte("a.dateEnd", ":enddate")
                )
            )
        )->setParameters(
            new ArrayCollection(
                [
                    new Parameter('startdate', $startDate, Types::DATETIME_IMMUTABLE),
                    new Parameter('enddate', $endDate, Types::DATETIME_IMMUTABLE)
                ]
            )
        );
    }
}
