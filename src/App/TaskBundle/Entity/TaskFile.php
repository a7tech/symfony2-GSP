<?php

namespace App\TaskBundle\Entity;

use App\MediaBundle\Entity\Image;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vlabs\MediaBundle\Annotation\Vlabs;

/**
 * @ORM\Entity
 * @ORM\Table(name="task_file")
 */
class TaskFile
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
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\MediaBundle\Entity\Image", cascade={"persist", "remove"}, orphanRemoval=true))
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     * })
     *
     * @Vlabs\Media(identifier="media_image", upload_dir="upload/task_files")
     * @Assert\Valid()
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="files")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     **/
    private $task;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param Task $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

}