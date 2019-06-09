<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Categories;
use Doctrine\ORM\EntityRepository;

class CategoriesRepository extends EntityRepository
{
    public function findByName($name)
    {
        /**
         * @param $name
         * return mixed
         */
        return $this->createQueryBuilder('d')
            ->where("d.name = :name")
            ->setParameter("name", $name)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $departments = [];
        /** @var Categories $categories */
        foreach ($this->findAll() as $categories) {
            $departments[$categories->getName()] = $categories->getId();
        }

        return $departments;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findAllByDepartment($id)
    {
        return $this->createQueryBuilder('d')
            ->where("d.department = :id")
            ->setParameter("id", $id)
            ->getQuery()
            ->execute();
    }
}