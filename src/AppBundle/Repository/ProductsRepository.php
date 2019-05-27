<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository
{
    public function findByName($name)
    {
        /**
         * @param $name
         * return mixed
         */
        return $this->createQueryBuilder('p')
            ->where("p.name = :name")
            ->setParameter("name", $name)
            ->getQuery()
            ->execute();
    }
}