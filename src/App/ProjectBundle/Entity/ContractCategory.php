<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-04-17
 * Time: 13:03
 */

namespace App\ProjectBundle\Entity;

use App\CategoryBundle\Entity\Category as CommonCategory;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Project category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="project_contract_category")
 * @ORM\Entity(repositoryClass="App\CategoryBundle\Entity\CategoryRepository")
 */
class ContractCategory extends CommonCategory
{
    /**
     * @var Project
     * 
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $project;

    /***************************************
     * Override mapping
     ***************************************/

    /**
     * @var Category|null
     *
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="ContractCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ContractCategory", mappedBy="parent", cascade={"all"})
     */
    protected $children;

    /***************************************
     * End Override mapping
     ***************************************/

    public function __construct(Category $category = null)
    {
        if($category !== null){
            $this->title = $category->getTitle();
            $this->description = $category->getDescription();
            $this->slug = $category->getSlug();
        }
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
    }


} 