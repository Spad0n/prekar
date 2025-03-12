<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * Fetch offers based on filters.
     */
    public function findFilteredOffers(array $filters, bool $isAdmin)
    {
        $qb = $this->createQueryBuilder('o')
            ->join('o.car', 'c')
            ->orderBy('o.price', 'ASC');

        // If not admin, filter only available offers
        if (!$isAdmin) {
            $qb->andWhere('o.available = :available')
            ->setParameter('available', 'available');
        }

        // Apply location filter
        if (!empty($filters['locations']) && !in_array('Any', $filters['locations'], true)) {
            $qb->andWhere('o.localisationGarage IN (:locations)')
            ->setParameter('locations', $filters['locations']);
        }

        // Apply delivery filter
        if (!empty($filters['delivery']) && !in_array('Any', $filters['delivery'], true)) {
            $qb->andWhere('o.delivery IN (:delivery)')
                ->setParameter('delivery', $filters['delivery']);
        }

        // Apply car filters
        if (!empty($filters['brand'])) {
            $qb->andWhere('c.brand IN (:brand)')
            ->setParameter('brand', $filters['brand']);
        }
        if (!empty($filters['fuelType'])) {
            $qb->andWhere('c.fuelType IN (:fuelType)')
            ->setParameter('fuelType', $filters['fuelType']);
        }
        if (!empty($filters['nbSeat'])) {
            $qb->andWhere('c.nbSeat IN (:nbSeat)')
            ->setParameter('nbSeat', $filters['nbSeat']);
        }
        if (!empty($filters['bootCapacity'])) {
            $qb->andWhere('c.bootCapacity IN (:bootCapacity)')
            ->setParameter('bootCapacity', $filters['bootCapacity']);
        }

        // Apply price range filter
        $qb->andWhere('o.price BETWEEN :minPrice AND :maxPrice')
        ->setParameter('minPrice', $filters['minPrice'])
        ->setParameter('maxPrice', $filters['maxPrice']);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the highest price from offers.
     */
    public function getMaxPrice(): int
    {
        return max(0, $this->createQueryBuilder('o')
            ->select('MAX(o.price)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }

    /**
     * Get the lowest price from offers.
     */
    public function getMinPrice(): int
    {
        return min(0, $this->createQueryBuilder('o')
            ->select('MIN(o.price)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }

    /**
     * Get distinct locations.
     */
    public function getDistinctLocations(): array
    {
        return array_map(
            fn($l) => $l['localisationGarage'],
            $this->createQueryBuilder('o')
                ->select('DISTINCT o.localisationGarage')
                ->where('o.localisationGarage IS NOT NULL')
                ->getQuery()
                ->getResult()
        );
    }

    //    /**
    //     * @return Offer[] Returns an array of Offer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Offer
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
