<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Categories;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class CategoriesRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('categories')
            ->orderBy('categories.name', 'ASC');
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

    /**
     * @param $categoryName
     * @return bool
     */
    public function checkCategoriesName($categoryName)
    {
        if ($this->findBy(["name" => $categoryName])) {
            return false;
        }
        return true;
    }
}