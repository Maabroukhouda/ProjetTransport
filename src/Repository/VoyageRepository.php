<?php

namespace App\Repository;

use App\Entity\Voyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voyage[]    findAll()
 * @method Voyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voyage::class);
    }
    public function filter(?int $min_prix, ?int $max_prix, ?int $min_nb_place, $depart, $destination, bool $includeUnavailableProducts = false): array
    {
        $qb = $this->createQueryBuilder('V')
            ->select('V');

        if ($min_prix != null) {
            $qb = $qb->where('V.prix >= :minprix')
                ->setParameter('minprix', $min_prix);
        }

        if ($max_prix != null) {
            $qb = $qb->andWhere('V.prix <= :maxprix')
                ->setParameter('maxprix', $max_prix);
        }

        if ($min_nb_place != null) {
            $qb = $qb->andWhere('V.nbplace >= :nb_place')
                ->setParameter('nb_place', $min_nb_place);
        }
        if ($depart != null) {
            $qb = $qb->andWhere('V.depart = :depart')
                ->setParameter('depart', $depart);
        }

        if ($destination != null) {
            $qb = $qb->andWhere('V.destination = :destination')
                ->setParameter('destination', $destination);
        }


        $query = $qb->getQuery();
        return $query->execute();
    }

    // /**
    //  * @return Voyage[] Returns an array of Voyage objects
    //  */
    /*
     * public function
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Voyage
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
