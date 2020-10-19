<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders", options={"collate"="utf8mb4_unicode_ci"})
 */
class Order
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\Column(type="integer")
     */
    private $userid;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $orderCode;

    /**
     * @ORM\ManyToMany(targetEntity="Product")
     * @ORM\Column(type="integer")
     */
    private $productid;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @ORM\Column(type="datetime")
    */
    private $shippingDate;


    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    public function setOrderCode($orderCode)
    {
        $this->orderCode = $orderCode;
    }

    public function setProductid($productid)
    {
        $this->productid = $productid;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function setShippingDate($shippingDate)
    {
        $this->shippingDate = $shippingDate;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userid;
    }

    public function getOrderCode()
    {
        return $this->orderCode;
    }

    public function getProductId()
    {
        return $this->productid;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getShippingDate()
    {
        return $this->shippingDate;
    }

}