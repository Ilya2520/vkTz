<?php

namespace App\Repository;

use App\Entity\CompletedQuest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompletedQuest>
 *
 * @method CompletedQuest|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompletedQuest|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompletedQuest[]    findAll()
 * @method CompletedQuest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompletedQuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompletedQuest::class);
    }

    //    /**
    //     * @return CompletedQuest[] Returns an array of CompletedQuest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CompletedQuest
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
