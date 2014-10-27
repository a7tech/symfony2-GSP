<?php
namespace App\ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\CategoryBundle\Entity\Category as CommonCategory;

/**
 * Project category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="project_category")
 * @ORM\Entity(repositoryClass="App\CategoryBundle\Entity\CategoryRepository")
 */
class Category extends CommonCategory
{
    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="categories")
     */
    protected $categories;

    public function __construct()
    {
        parent::__construct();

        $this->categories = new ArrayCollection();
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getProjects()
    {
        return $this->categories;
    }
}