<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Product[]
     */
    public function findAllGreaterThanPrice($minPrice, $maxPrice, $cat): array
    {
        $entityManager = $this->getEntityManager();

        // $query = $entityManager->createQuery(
        //     'SELECT p
        //     FROM App\Entity\Product p
        //     WHERE p.Category = :Category AND p.Price >= :minP AND p.Price <= :maxP
        //     ORDER BY p.Price ASC'
        // )->setParameter('minP', $minPrice)
        //     ->setParameter('maxP', $maxPrice)
        //     ->setParameter('Category', $cat);
        // returns an array of Product objects
        // return $query->getResult();
        if ($minPrice == NULL && $maxPrice == NULL && $cat == NULL)
            return $this->findAllProduct();
        $qb = $entityManager->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Product', 'p')
            ->where('p.Price >= 0    ');
        if ($minPrice != NULL) {
            $qb->andWhere('p.Price >=' . $minPrice);
        }
        if ($maxPrice != NULL) {
            $qb->andWhere('p.Price <=' . $maxPrice);
        }
        if ($cat == NULL || $cat == 0) {}
        else {
            $qb->andWhere('p.Category    =' . $cat);}


        // returns an array of Product objects
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Product[]
     */
    public function findAllProduct(): array
    {
        $entityManager = $this->getEntityManager();

         $query = $entityManager->createQuery(
             'SELECT p
             FROM App\Entity\Product p');
         //returns an array of Product objects
         return $query->getResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
