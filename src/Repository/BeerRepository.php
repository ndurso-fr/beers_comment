<?php

namespace App\Repository;

use App\Entity\Beer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @extends ServiceEntityRepository<Beer>
 *
 * @method Beer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Beer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Beer[]    findAll()
 * @method Beer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beer::class);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getBeerByName(string $name):array {
            $queryBuilder = $this->createQueryBuilder('b')
                ->where('b.name = :name')
                ->setParameter('name', $name)
            ;
        return $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @return array
     */
    public function findAllBeer(): array {
        return $this->createQueryBuilder('b')
            ->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
    public function searchBearName($searched):array {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('b.name')
            ->where('UPPER(b.name) LIKE :searched')
            ->setParameter('searched', '%' . strtoupper($searched) . '%')
            ->orderBy('b.name', 'ASC')
            ;
//dump($queryBuilder->getQuery()->getDQL(), $queryBuilder->getParameters());
        //dd($queryBuilder->getQuery()->getDQL());
//        var_dump($queryBuilder->getParameters());
        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Beer[] Returns an array of Beer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Beer
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
