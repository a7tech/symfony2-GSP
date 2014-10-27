<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.02.14
 * Time: 14:28
 */

namespace App\TaxBundle\Entity;

use App\CompanyBundle\Entity\CompanyCopy;
use Doctrine\ORM\Mapping as ORM;

/**
 * Taxation
 *
 * @ORM\Table(name="taxation_copy")
 * @ORM\Entity()
 */
class TaxationCopy implements TaxationInfoInterface
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
     * @var Taxation
     *
     * @ORM\ManyToOne(targetEntity="Taxation")
     * @ORM\JoinColumn(name="taxation_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $originalTaxation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float")
     */
    protected $rate;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="string", length=50)
     */
    protected $number;

    /**
     * @var CompanyCopy
     *
     * @ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\CompanyCopy")
     * @ORM\JoinColumn(name="company_copy_id", referencedColumnName="id")
     */
    protected $companyCopy;

    public function __construct(Taxation $taxation, CompanyCopy $companyCopy = null)
    {
        $this->name = $taxation->getName();
        $this->rate = $taxation->getRate(false);
        $this->number = $taxation->getNumber();

        $this->originalTaxation = $taxation;
        $this->companyCopy = $companyCopy;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return \App\TaxBundle\Entity\Taxation
     */
    public function getOriginalTaxation()
    {
        return $this->originalTaxation;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }
}