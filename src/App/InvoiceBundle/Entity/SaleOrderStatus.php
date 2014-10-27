<?php

namespace App\InvoiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseOrderStatus
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PurchaseOrderStatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;





}
