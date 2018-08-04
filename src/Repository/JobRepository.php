<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Job;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function createPaginatorForJobsByCategory(Category $category)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where('j.category = :category')
            ->setParameter('category', $category)
        ;

        $qb->andWhere('j.expiresAt > :now')
            ->setParameter('now', new \DateTime('now'))
        ;

        return $this->getPaginator($qb);
    }
}
