<?php

namespace App\Repository;

use App\Entity\Albums;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Albums>
 *
 * @method Albums|null find($id, $lockMode = null, $lockVersion = null)
 * @method Albums|null findOneBy(array $criteria, array $orderBy = null)
 * @method Albums[]    findAll()
 * @method Albums[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Albums::class);
    }

    public function save(Albums $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Albums $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Albums[] Returns an array of Albums objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Albums
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
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
        $qb = $this->createQueryBuilder("s");
        $qb->add(
            'where',
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gte("s.dateStart", ":startdate"),
                    $qb->expr()->lte("s.dateStart", ":enddate")
                ),
                $qb->expr()->andX(
                    $qb->expr()->gte("s.dateEnd", ":startdate"),
                    $qb->expr()->lte("s.dateEnd", ":enddate")
                )
            )
        )->setParameters(
            new ArrayCollection(
                [
                    new Parameter('startdate', $startDate, Types::DATETIME_IMMUTABLE),
                    new Parameter('enddate', $endDate, Types::DATETIME_IMMUTABLE)
                ]
            )

            // A FAIRE UNE FOIS RENTRER
        );
    }
}
