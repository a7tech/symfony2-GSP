<?php
/**
 * TaskTypeType
 */

namespace App\TaskBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class TaskTypeType extends AbstractType
{
    public function getName()
    {
        return 'task_type';
    }

    public function getParent()
    {
        return 'entity';
    }
}