<?php

namespace App\InvoiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceProductTaxes
 *
 * @ORM\Table(name="invoice_product_taxes")
 * @ORM\Entity(repositoryClass="App\InvoiceBundle\Entity\InvoiceProductTaxesRepository")
 */
class InvoiceProductTaxes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Taxes
     * @ORM\ManyToOne(targetEntity="App\TaxBundle\Entity\TaxType")
     * @ORM\JoinColumn(name="tax_id", referencedColumnName="id")
     */
    private $tax;

    /**
     * @var string
     * @ORM\Column(name="tax_name", type="string")
     */
    private $taxName;

    /**
     * @var string
     * @ORM\Column(name="tax_rate", type="float")
     */
    private $taxRate;

    /**
     * @ORM\ManyToOne(targetEntity="InvoiceProduct", inversedBy="taxes")
     * @ORM\JoinColumn(name="invoice_product_id", referencedColumnName="id")
     **/
    private $product;

    public function __toString() {

        return $this->getTaxName().' ('.$this->getTaxRate().'%)';
    }

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
     * @return \App\InvoiceBundle\Entity\Taxes
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @return string
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * @return string
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param \App\InvoiceBundle\Entity\Taxes $tax
     */
    public function setTax($tax)
    {
        $this->setTaxName($tax->getName());
        $this->setTaxRate($tax->getRate());
        $this->tax = $tax;
    }

    /**
     * @param string $taxName
     */
    public function setTaxName($taxName)
    {
        $this->taxName = $taxName;
    }

    /**
     * @param string $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }


}
