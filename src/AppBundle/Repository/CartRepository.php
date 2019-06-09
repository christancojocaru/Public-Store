<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class CartRepository extends EntityRepository
{
    /**
     * @param $user
     * @param $product
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findByUserAndProduct($user, $product)
    {
        return $this->createQueryBuilder('c')
            ->where("c.user = :user")
            ->andWhere("c.product = :product")
            ->setParameter("user", $user)
            ->setParameter("product", $product)
            ->getQuery()
            ->getOneOrNullResult();
    }
}