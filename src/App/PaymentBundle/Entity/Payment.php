<?php

namespace App\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Table(name="payments")
 * @ORM\Entity(repositoryClass="App\PaymentBundle\Entity\PaymentRepository")
 */
class Payment
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
     * @var float
     * @Assert\GreaterThan(value=0, message="Value must be greater than 0")
     * @Assert\Type(type="float", message="Only numeric values allowed")
     * @ORM\Column(name="amount", type="float")
     */
    protected $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\PaymentBundle\Entity\PaymentMethod", inversedBy="payments")
     * @ORM\JoinColumn(name="payment_method_type_id", referencedColumnName="id")
     **/
    protected $paymentMethod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_deadline", type="date")
     */
    protected $maturity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_date", type="date", nullable=true)
     */
    protected $paymentDate;


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
     * Set amount
     *
     * @param $amount
     * @internal param $integer $4amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = floatval($amount);

        return $this;
    }

    /**
     * Get 4amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $payment_method
     */
    public function setPaymentMethod($payment_method)
    {
        $this->paymentMethod = $payment_method;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set paymentDeadline
     *
     * @param \DateTime $paymentDeadline
     * @return Payment
     */
    public function setMaturity(\DateTime $paymentDeadline)
    {
        $this->maturity = $paymentDeadline;

        return $this;
    }

    /**
     * Get paymentDeadline
     *
     * @return \DateTime
     */
    public function getMaturity()
    {
        return $this->maturity;
    }

    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     * @return Payment
     */
    public function setPaymentDate(\DateTime $paymentDate = null)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }


}
