<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Products::class);
    }


    public function countProducts($params)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('Count(p.id)');
        if($params['category'] != null ){
            $qb->where('p.category like :category')
                ->setParameter('category', '%'.$params['category'].'%');
        }

        return $qb->getQuery()->getSingleResult();
    }


    public function getCategories()
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.category')
            ->groupBy('p.category');

        return $qb->getQuery()->getResult();
    }


}
