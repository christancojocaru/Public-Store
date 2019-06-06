<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 * @ORM\Table(
 *     name="cart",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"product_id", "user_id"})}
 *     )
 * @UniqueEntity(
 *     fields={"product_id", "user_id"},
 *     message="Product for given user already exists in database."
 * )

 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(
     *     name="product_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        $price = $this->product->getPrice();
        return $this->quantity * $price;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}