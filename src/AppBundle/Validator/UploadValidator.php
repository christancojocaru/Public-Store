<?php


namespace AppBundle\Validator;


use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class UploadValidator
{
    const NAME = 0;
    const CATEGORY = 1;
    const PRICE = 2;
    const STOCK = 3;
    const DECIMAL = 4;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param $data
     * @return array
     */
    public function validate($data)
    {
        $errors = [];
        $names = [];
        foreach ($data as $rowKey => $rowValue) {
            //check if every field is empty
            if ($result = $this->checkEmpty($rowValue)) {
                $errors[$rowKey + 1] = $result;
            }
            //check if field price is correct
            if ($rowValue[self::PRICE] <= 0 ) {
                $errors[$rowKey + 1][self::PRICE + 1] = "Price less than 0";
            }elseif ($this->checkDecimal($rowValue[self::PRICE])) {
                $errors[$rowKey + 1][self::PRICE + 1] = "Decimal Incorrect";
            }
            //check if field stock is correct
            if (intval($rowValue[self::STOCK] <= 0 )) {
                $errors[$rowKey + 1][self::STOCK +1] = "Stock less than 0";
            }
            //check if field category exist in Database
            $categoryRepository = $this->em->getRepository(Categories::class);
            if ($categoryRepository->checkCategoriesName($rowValue[self::CATEGORY])) {
                $errors[$rowKey +1][self::CATEGORY +1] = "Category don't exist in Database";
            }

            $names[$rowKey + 1] = $rowValue[self::NAME];
        }

        $namesUnique = array_unique($names);
        $namesDuplicate = array_diff_assoc($names, $namesUnique);
        //check if are duplicates by name in input file
        if (count($namesDuplicate) > 1) {
            foreach ($namesDuplicate as $key => $value) {
                $errors[$key][self::NAME + 1][] = "Duplicate Name";
            }
        }
        //check if exists by name in database
        $productRepository = $this->em->getRepository(Product::class);
        $products = $productRepository->checkProductName($namesUnique);//return products which are already in database by name
        if (!empty($products)) {
            $namesExist = array_intersect($names, $products);
            foreach ($namesExist as $key => $value) {
                $errors[$key][self::NAME + 1][] = "Name exist in DB";
            }
        }

        ksort($errors);
        return $errors;
    }

    /**
     * @param $row array
     * @return array
     */
    private function checkEmpty($row): array
    {
        $errors = [];
        foreach ( $row as $cellKey => $cellValue ) {
            if ($cellValue == "") {
                $errors[$cellKey + 1] = 'Empty value';
            }
        }
        return $errors;
    }

    /**
     * @param $value
     * @return bool
     */
    private function checkDecimal($value)
    {
        $diff = strlen($value) - strpos($value, ".");
        if ($diff != (self::DECIMAL + 1)) {
            return true;
        }
        return false;
    }

    /**
     * @param EntityManagerInterface $em
     * @required
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }
}