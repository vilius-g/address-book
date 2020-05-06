<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SharedContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SharedContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method SharedContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method SharedContact[]    findAll()
 * @method SharedContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SharedContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SharedContact::class);
    }

    // /**
    //  * @return SharedContact[] Returns an array of SharedContact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SharedContact
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
