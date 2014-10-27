<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 06.03.14
 * Time: 13:30
 */

namespace App\TaskBundle\Form\Subscriber;


use App\StatusBundle\Utils\StatusTranslator;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Entity\TaskBase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskLockSubscriber implements EventSubscriberInterface
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * @var StatusTranslator
     */
    protected $statusTranslator;

    public function __construct(StatusTranslator $statusTranslator)
    {
        $this->statusTranslator = $statusTranslator;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'addFields'
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $this->task = $event->getData();
        $this->addFields($event);
    }

    public function addFields(FormEvent $event)
    {
        $form = $event->getForm();

        $types = $this->statusTranslator->getStatusesNames(Task::TYPES_GROUP_NAME);
        $is_locked = $this->task->isLocked();

        $form->add('project', 'project_choice', array(
                'label' => 'project',
                'translation_domain' => 'Project',
                'required' => true,
                'attr' => [
                    'class' => 'project-select'
                ],
                'empty_value' => 'Choose project',
                'disabled' => $is_locked,
                'property' => 'nameWithId'
            ))
            ->add('name', 'text', array(
                'label' => 'name',
                'translation_domain' => 'Common',
                'required' => true,
                'disabled' => $is_locked && $this->task->isContracted(),
                'attr' => [
                    'class' => 'input-xxlarge'
                ]
            ))
            ->add('taskDescription', 'textarea', array(
                'label' => 'task_details',
                'attr'=> array('class' => 'tinymce', 'data-theme' => 'simple'),
                'required' => false,
                'disabled' => $is_locked
            ))
            ->add('type', 'choice', array(
                'label' => 'type',
                'choices' => $types,
                'required' => true,
                'attr' => [
                    'class' => 'task-type',
                    'data-payable' => TaskBase::TYPE_PAYABLE,
                    'data-adjustment' => TaskBase::TYPE_ADJUSTMENT
                ],
                'disabled' => $is_locked
            ));

    }

} 