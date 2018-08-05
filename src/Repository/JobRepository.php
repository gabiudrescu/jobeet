<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Job;
use Doctrine\ORM\QueryBuilder;
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

        $this->setActiveJobs($qb);

        return $this->getPaginator($qb);
    }
    
    public function createPaginatorForSearch(string $query)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where("j.company like :query")
            ->orWhere("j.description like :query")
            ->orWhere("j.location like :query")
            ->orWhere("j.howToApply like :query")
            ->orWhere("j.position like :query")
        ;

        $qb->setParameter('query', "%" . $query . "%");

        $this->setActiveJobs($qb);

        return $this->getPaginator($qb);
    }

    private function setActiveJobs(QueryBuilder $builder)
    {
        $builder->andWhere('j.expiresAt > :now')
            ->setParameter('now', new \DateTime('now'))
        ;
    }

    public function findJobsForCategory(Category $category, int $limit = 6)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->select('j, c')
            ->join('j.category', 'c', 'with', 'j.category = :category')
            ->setParameter('category', $category)
            ->setMaxResults($limit);
        ;

        $this->setActiveJobs($qb);

        return $qb->getQuery()->getResult();
    }

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

    public function findJobsExpiringIn(int $days)
    {
        $qb = $this->createQueryBuilder('j');

        $qb->where('j.expiresAt between :today and :ndays')
            ->setParameter('today', new \DateTime('now'))
            ->setParameter('ndays', new \DateTime(sprintf('+%ddays', $days)))
        ;

        foreach ($qb->getQuery()->iterate() as $result)
        {
            yield $result[0];

            $this->_em->clear();
        }
    }
}
