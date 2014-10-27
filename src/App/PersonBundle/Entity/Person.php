<?php

namespace App\PersonBundle\Entity;

use App\AddressBundle\Entity\Address;
use App\EmailBundle\Entity\Email;
use App\EmploymentBundle\Entity\Employment;
use App\HrBundle\Entity\HrInfo;
use App\HrBundle\Entity\SalaryInfo;
use App\InvoiceBundle\Entity\Invoice;
use App\SkillBundle\Entity\Skill;
use App\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Person
 *
 * @ORM\Table(name="persons")
 * @ORM\Entity(repositoryClass="App\PersonBundle\Entity\PersonRepository")
 */
class Person
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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="App\UserBundle\Entity\User", mappedBy="person")
     *
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string")
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    protected $gender;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     */
    protected $birthDate;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="\App\AddressBundle\Entity\Address", cascade={"all"}, inversedBy="persons")
     * @ORM\JoinTable(name="person_addresses",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $addresses;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="\App\PhoneBundle\Entity\Phone", cascade={"all"})
     * @ORM\JoinTable(name="person_phones",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="phone_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $phones;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\EmailBundle\Entity\Email", mappedBy="person", cascade={"all"})
     *
     */
    protected $emails;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\App\SocialMediaBundle\Entity\SocialMedia", cascade={"all"})
     * @ORM\JoinTable(name="person_social_medias",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="social_media_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $socialMedias;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="PersonGroup")
     * @ORM\JoinTable(name="persons_groups",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $personGroup;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\EmploymentBundle\Entity\Employment", mappedBy="person", cascade={"all"})
     */
    private $employments;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\SkillBundle\Entity\Skill", mappedBy="person", cascade={"all"})
     */
    private $skills;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\Education", cascade={"all"})
     * @ORM\JoinTable(name="person_education",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="education_id", referencedColumnName="id")}
     * )
     */
    private $educations;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\WorkExperience", cascade={"all"})
     * @ORM\JoinTable(name="person_work_experience",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="work_experience_id", referencedColumnName="id")}
     * )
     */
    private $workExperiences;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\Association", cascade={"all"})
     * @ORM\JoinTable(name="person_association",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="association_id", referencedColumnName="id")}
     * )
     */
    private $associations;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\LangExperience", cascade={"all"})
     * @ORM\JoinTable(name="person_lang_experience",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="lang_experience_id", referencedColumnName="id")}
     * )
     */
    private $languages;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\Certification", cascade={"all"})
     * @ORM\JoinTable(name="person_certification",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="certification_id", referencedColumnName="id")}
     * )
     */
    private $certifications;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\MilitaryHistory", cascade={"all"})
     * @ORM\JoinTable(name="person_military_history",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="military_history_id", referencedColumnName="id")}
     * )
     */
    private $militaryHistories;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\CvBundle\Entity\Reference", cascade={"all"})
     * @ORM\JoinTable(name="person_reference",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reference_id", referencedColumnName="id")}
     * )
     */
    private $references;

    /**
     * @var HrInfo
     *
     * @ORM\ManyToOne(targetEntity="App\HrBundle\Entity\HrInfo", cascade={"all"})
     * @ORM\JoinColumn(name="hr_info_id", referencedColumnName="id", nullable=true)
     */
    private $hrInfo;

    /**
     * @var SalaryInfo
     *
     * @ORM\ManyToOne(targetEntity="App\HrBundle\Entity\SalaryInfo", cascade={"all"})
     * @ORM\JoinColumn(name="salary_info_id", referencedColumnName="id", nullable=true)
     */
    private $salaryInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="objective", type="text", nullable=true)
     */
    private $objective;

    /**
     * @var string
     * @ORM\OneToOne(targetEntity="PersonImage", cascade={"all"})
     * @ORM\JoinColumn(name="person_image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @var Invoice
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\SaleOrder", mappedBy="customer")
     */
    private $customerInvoice;

    /**
     * @var Invoice
     * @ORM\OneToMany(targetEntity="App\InvoiceBundle\Entity\SaleOrder", mappedBy="vendor")
     */
    private $vendorInvoice;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->socialMedias = new ArrayCollection();
        $this->employments = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->personGroup = new ArrayCollection();
        $this->educations = new ArrayCollection();
        $this->workExperiences = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->militaryHistories = new ArrayCollection();
        $this->references = new ArrayCollection();
        $this->customerInvoice = new ArrayCollection();
        $this->vendorInvoice = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getName()
    {
        return $this->getFirstName().' '.$this->getLastName();
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
     * @param Collection $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * @return Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    public function getMainAddress()
    {
        foreach($this->addresses as $address){
            /** @var Address $address */
            if($address->getIsMain()){
                return $address;
            }
        }

        return null;
    }

    public function getBillingAddress()
    {
        foreach($this->addresses as $address){
            /** @var Address $address */
            if($address->getIsBilling()){
                return $address;
            }
        }

        return null;
    }

    public function getShippingAddress()
    {
        foreach($this->addresses as $address){
            /** @var Address $address */
            if($address->getIsShipping()){
                return $address;
            }
        }

        return null;
    }

    /**
     * @param DateTime $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param Collection $employments
     */
    public function setEmployments($employments)
    {
        $this->employments = $employments;
    }

    /**
     * @return Collection
     */
    public function getEmployments()
    {
        return $this->employments;
    }

    /**
     * Has Employments
     *
     * @return bool
     */
    public function hasEmployments()
    {
        return !$this->employments->isEmpty();
    }

    /**
     * Add Employment
     *
     * @param Employment $employment
     * @return Person
     */
    public function addEmployment(Employment $employment)
    {
        if (!$this->hasEmployment($employment)) {
            $this->employments->add($employment);
        }

        return $this;
    }

    /**
     * Remove Employement
     *
     * @param Employment $employment
     * @return Person
     */
    public function removeEmployment(Employment $employment)
    {
        if ($this->hasEmployment($employment)) {
            $this->employments->removeElement($employment);
        }

        return $this;
    }

    /**
     * Has Employer
     *
     * @param Employment $employment
     * @return bool
     */
    public function hasEmployment(Employment $employment)
    {
        return $this->employments->contains($employment);
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Collection $emails
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    /**
     * @return Collection
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Has Emails
     *
     * @return bool
     */
    public function hasEmails()
    {
        return !$this->emails->isEmpty();
    }

    /**
     * Add Email
     *
     * @param Email $email
     * @return Person
     */
    public function addEmail(Email $email)
    {

        if (!$this->hasEmail($email)) {
            $this->emails->add($email);
            $email->setPerson($this);
        }

        return $this;
    }

    /**
     * Remove Employement
     *
     * @param Email $email
     * @return Person
     */
    public function removeEmail(Email $email)
    {
        if ($this->hasEmail($email)) {
            $this->emails->removeElement($email);
        }

        return $this;
    }

    /**
     * Has Employer
     *
     * @param Email $email
     * @return bool
     */
    public function hasEmail(Email $email)
    {
        return $this->emails->contains($email);
    }
    

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * isMale
     *
     * @return bool
     */
    public function isMale()
    {
        return $this->gender == 'm';
    }

    /**
     * isFemale
     *
     * @return bool
     */
    public function isFemale()
    {
        return $this->gender == 'f';
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param Collection $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @return Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param Collection $socialMedias
     */
    public function setSocialMedias($socialMedias)
    {
        $this->socialMedias = $socialMedias;
    }

    /**
     * @return Collection
     */
    public function getSocialMedias()
    {
        return $this->socialMedias;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param PersonGroup $personGroup
     */
    public function setPersonGroup($personGroup)
    {
        $this->personGroup = $personGroup;
    }

    /**
     * @return PersonGroup
     */
    public function getPersonGroup()
    {
        return $this->personGroup;
    }

    /**
     * @return Collection
     */
    public function getSkills()
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill)
    {
        $this->skills->add($skill);
        $skill->setPerson($this);
    }

    public function removeSkill(Skill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * getSkillsBySpecialityAndSkillCategory
     *
     * @return array list($skillsBySpeciality, $skills)
     */
    public function getSkillsGrouped()
    {
        $skillsGrouped = array();
        $specialities = array();

        /** @var Skill $skill */
        foreach ($this->skills as $skill) {
            $specId = $skill->getSpeciality()->getId();
            if (isset($skillsGrouped[$specId])) {
                $skillsGrouped[$specId][] = $skill;
            } else {
                $specialities[$specId] = $skill->getSpeciality();
                $skillsGrouped[$specId] = array($skill);
            }
        }

        return array($skillsGrouped, $specialities);
    }

    public function getEducations()
    {
        return $this->educations;
    }

    public function setEducations($educations)
    {
        $this->educations = $educations;
    }

    public function getWorkExperiences()
    {
        return $this->workExperiences;
    }

    public function setWorkExperiences($workExperiences)
    {
        $this->workExperiences = $workExperiences;
    }

    public function getAssociations()
    {
        return $this->associations;
    }

    public function setAssociations($associations)
    {
        $this->associations = $associations;
    }

    public function getLanguages()
    {
        return $this->languages;
    }

    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    public function getCertifications()
    {
        return $this->certifications;
    }

    public function setCertifications($certifications)
    {
        $this->certifications = $certifications;
    }

    public function getMilitaryHistories()
    {
        return $this->militaryHistories;
    }

    public function setMilitaryHistories($militaryHistory)
    {
        $this->militaryHistories = $militaryHistory;
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function setReferences($references)
    {
        $this->references = $references;
    }

    public function getObjective()
    {
        return $this->objective;
    }

    public function setObjective($objective)
    {
        $this->objective = $objective;
    }

    /**
     * isEmptyCv
     *
     * @return bool
     */
    public function isEmptyCv()
    {
        return !$this->educations
            && !$this->languages
            && !$this->certifications
            && !$this->workExperiences
            && !$this->associations
            && !$this->militaryHistories
            && !$this->references
            && !$this->objective
            ;
    }

    /**
     * @param HrInfo $hrInfo
     */
    public function setHrInfo($hrInfo)
    {
        $this->hrInfo = $hrInfo;
    }

    /**
     * @return HrInfo
     */
    public function getHrInfo()
    {
        return $this->hrInfo;
    }

    /**
     * @param SalaryInfo $salaryInfo
     */
    public function setSalaryInfo($salaryInfo)
    {
        $this->salaryInfo = $salaryInfo;
    }

    /**
     * @return SalaryInfo
     */
    public function getSalaryInfo()
    {
        return $this->salaryInfo;
    }

    /**
     * @param string $image
     */
    public function setImage( $image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


}
