<?php
namespace App\InvoiceBundle\Entity\Returns;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductReturnReason
 *
 * @ORM\Table(name="invoice_product_return_reason")
 * @ORM\Entity(repositoryClass="App\InvoiceBundle\Entity\Returns\ProductReturnReasonRepository")
 */
class ProductReturnReason
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_stock_increased", type="boolean", options={"default"=false})
     */
    protected $isStockIncreased = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_stock_blocked", type="boolean", options={"default"=false})
     */
    protected $isStockBlocked = false;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductReturnReason
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set isStockIncreased
     *
     * @param boolean $isStockIncreased
     * @return ProductReturnReason
     */
    public function setIsStockIncreased($isStockIncreased)
    {
        $this->isStockIncreased = $isStockIncreased;
    
        return $this;
    }

    /**
     * Get isStockIncreased
     *
     * @return boolean 
     */
    public function getIsStockIncreased()
    {
        return $this->isStockIncreased;
    }

    /**
     * Set isStockBlocked
     *
     * @param boolean $isStockBlocked
     * @return ProductReturnReason
     */
    public function setIsStockBlocked($isStockBlocked)
    {
        $this->isStockBlocked = $isStockBlocked;
    
        return $this;
    }

    /**
     * Get isStockBlocked
     *
     * @return boolean 
     */
    public function getIsStockBlocked()
    {
        return $this->isStockBlocked;
    }

    function __toString()
    {
        return $this->name;
    }


}
