<?php

namespace App\EmploymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employment
 *
 * @ORM\Table(name="employments")
 * @ORM\Entity(repositoryClass="App\EmploymentBundle\Entity\EmploymentRepository")
 */
class Employment
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
     * @var
     * @ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\CommonCompany", inversedBy="employments")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $company;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\PersonBundle\Entity\Person", inversedBy="employments")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $person;

    /**
     * @var
     *
     *@ORM\ManyToOne(targetEntity="App\CompanyBundle\Entity\Department")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="department_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    protected $department;

    /**
     * @ORM\Column(name="role", type="string", nullable=true)
     * @var string
     */
    protected $role;

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
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }


}
