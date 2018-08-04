<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return mixed
     *
     * @Todo this should be moved to job repository and called for each category with a limit
     *       to avoid having all database hidrated by doctrine everytime the homepage loads
     */
    public function findCategoriesWithJobsForHomepage()
    {
        return $this->createQueryBuilder('c')
            ->select('c, j')
            ->join('c.jobs', 'j', 'with', 'j.expiresAt > :now')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getResult()
        ;
    }
}
