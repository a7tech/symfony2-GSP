<?php

namespace App\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\CategoryBundle\Entity\Category as CommonCategory;

/**
 * Product category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="product_category")
 * @ORM\Entity(repositoryClass="App\CategoryBundle\Entity\CategoryRepository")
 */
class Category extends CommonCategory
{
    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="categories")
     */
    protected $products;

    public function __construct()
    {
        parent::__construct();

        $this->products = new ArrayCollection();
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}