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
            if ( !isset($errors[$rowKey + 1][self::PRICE + 1]) ) {
                if ($rowValue[self::PRICE] <= 0 ) {
                    $errors[$rowKey + 1][self::PRICE + 1] = "'".$rowValue[self::PRICE]."' less or equal than 0";
                    if ($this->checkDecimal($rowValue[self::PRICE])) {
                        $errors[$rowKey + 1][self::PRICE + 1] .= " & only ".self::DECIMAL.' decimals';
                    };
                }elseif ($this->checkDecimal($rowValue[self::PRICE])) {
                    $errors[$rowKey + 1][self::PRICE + 1] = "Only ".self::DECIMAL.' decimals';
                };
            }
            //check if field stock is correct
            if ( !isset($errors[$rowKey + 1][self::STOCK + 1]) && intval($rowValue[self::STOCK] <= 0 )) {
                $errors[$rowKey + 1][self::STOCK +1] = "'".$rowValue[self::STOCK]."' less or equal than 0";
            }
            //check if field category exist in Database
            if ( !isset($errors[$rowKey + 1][self::CATEGORY + 1]) ) {
                $categoryRepository = $this->em->getRepository(Categories::class);
                if ($categoryRepository->checkCategoriesName($rowValue[self::CATEGORY])) {
                    $errors[$rowKey + 1][self::CATEGORY + 1] = "'".$rowValue[self::CATEGORY]."' don't exist in Database";
                }
            }

            $names[$rowKey + 1] = $rowValue[self::NAME];
        }

        $namesUnique = array_unique($names);
        $namesDuplicate = array_diff_assoc($names, $namesUnique);
        //check if are duplicates by name in input file
        if (count($namesDuplicate) > 1) {
            foreach ($namesDuplicate as $key => $value) {
                $errors[$key][self::NAME + 1] = "'".$value."' is duplicate";
            }
        }
        //check if exists by name in database
        $productRepository = $this->em->getRepository(Product::class);
        $products = $productRepository->checkProductName($namesUnique);//return products which are already in database by name
        if (!empty($products)) {
            $namesExist = array_intersect($names, $products);
            foreach ($namesExist as $key => $value) {
                if ( !isset($errors[$key][self::NAME + 1]) ) {
                    $errors[$key][self::NAME + 1] = "'".$value."' is duplicate & exist in Database";
                }else {
                    $errors[$key][self::NAME + 1] = "'".$value."' exist in Database";
                }
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