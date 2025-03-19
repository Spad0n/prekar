<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function getDistinctCarBrands(): array
    {
        return array_map(
            fn($c) => $c['brand'],
            $this->createQueryBuilder('c')
                ->select('DISTINCT c.brand')
                ->getQuery()
                ->getResult()
        );
    }

    public function getDistinctCarModels(): array
    {
        return array_map(
            fn($c) => $c['model'],
            $this->createQueryBuilder('c')
                ->select('DISTINCT c.model')
                ->getQuery()
                ->getResult()
        );
    }

    public function getDistinctCarFuelTypes(): array
    {
        return array_map(
            fn($c) => $c['fuelType'],
            $this->createQueryBuilder('c')
                ->select('DISTINCT c.fuelType')
                ->getQuery()
                ->getResult()
        );
    }

    public function getDistinctCarSeats(): array
    {
        return array_map(
            fn($c) => $c['nbSeat'],
            $this->createQueryBuilder('c')
                ->select('DISTINCT c.nbSeat')
                ->getQuery()
                ->getResult()
        );
    }

    public function getDistinctCarBootCapacities(): array
    {
        return array_map(
            fn($c) => $c['bootCapacity'],
            $this->createQueryBuilder('c')
                ->select('DISTINCT c.bootCapacity')
                ->getQuery()
                ->getResult()
        );
    }

    //    /**
    //     * @return Car[] Returns an array of Car objects
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

    //    public function findOneBySomeField($value): ?Car
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
