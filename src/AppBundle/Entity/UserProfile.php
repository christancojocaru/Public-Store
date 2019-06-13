<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_profile")
 * @UniqueEntity(fields={"email"}, message="This email is assigned to another user!")
 */
class UserProfile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @Assert\NotBlank()
     * @Assert\Email(message="The email {{ value }} is not a valid email.")
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min = 10, max = 10, exactMessage="Mobile Number should have exactly 10 numbers!")
     * @Assert\Regex(pattern="/^[0-9]*$/", message="Insert number only!")
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $country;

    /**
     * @return string
     */
    public function getFullName()
    {
        return ucfirst($this->firstName) . " " . ucfirst($this->lastName);
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }
}