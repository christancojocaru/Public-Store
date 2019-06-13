<?php


namespace AppBundle\Validator;


use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class UploadValidator2
{
    private $em;

    /**
     * UploadValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function firstStep($data)
    {
        $allNames = array();
        foreach ($data as $row) {
            $allNames[] = $row[0];
        }

        if ( count($allNames) != count(array_unique($allNames))) {
            $uniqNameError = array();
            foreach ($data as $key => $row ) {
                unset($allNames[$key]);
               $uniqNameError[] =  array_search($row[0], $allNames);
            }
        }

        foreach ($data as $key => $row) {
            $errors[$key] = $this->getError($row);
            if ( $row[2] < 0 ) {
                $error[] = "Price < 0";
            }
            if ( $row[3] <= 0 ) {
                $error[] ="Stock < 1";
            }
        }
        $errors[] = $uniqNameError;

        return $errors;
    }

    public function secondStep($data)
    {
        $errors = array();
        foreach ($data as $key => $row) {
            $error = array();
            $existCategory = $this->em->getRepository(Categories::class)->checkCategoriesName($row[1]);
            if (!$existCategory) {
                $error[] = "Categories don't exists in Database!";
                $errors[$key] = $error;
//                exit();
            }
            $existName = $this->em->getRepository(Product::class)->checkProductName($row[0]);
            if ($existName) {
                $error[] = "Name already exists in Database!";
                $errors[$key] = $error;
            }
        }

        return $errors;
    }

    protected function getError($row)
    {
        $error = array();
        foreach ( $row as $key => $column ) {
            if ( empty($column) ) {
                array_push($error, $key);
            }
        }
        return $error;
    }
}