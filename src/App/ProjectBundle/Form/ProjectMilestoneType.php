<?php

namespace App\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\StatusBundle\Utils\StatusTranslator;
use App\ProjectBundle\Entity\ProjectMilestone;

class ProjectMilestoneType extends AbstractType
{
    protected $statusTranslator;

    function __construct(StatusTranslator $statusTranslator)
    {
        $this->statusTranslator = $statusTranslator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $status = $this->statusTranslator->getStatusesNames(ProjectMilestone::STATUS_GROUP_NAME);

        $builder
            ->add('name', null, [
                    'label' => 'name',
                    'translation_domain' => 'Common'
                ])
            ->add('description', null, [
                'label' => 'description',
                'translation_domain' => 'Common'
            ])
            ->add('status', 'choice', [
                'label' => 'status',
                'choices' => $status,
                'required' => true,
                'attr' => array('class' => 'form-control')
            ])
            //->add('color')
            ->add('value', null, array(
                'label' => 'milestone.importance',
                'required' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ProjectBundle\Entity\ProjectMilestone',
            'translation_domain' => 'Project'
        ));
    }

    public function getName()
    {
        return 'project_milestone_type';
    }
}