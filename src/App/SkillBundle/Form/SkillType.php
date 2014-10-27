<?php
/**
 * SkillType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 20.08.13 16:32
 */

namespace App\SkillBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkillType extends AbstractType
{
    /**
     * @var EventSubscriberInterface|null
     */
    protected $eventSubscriber;

    public function __construct(EventSubscriberInterface $eventSubscriber = null)
    {
        $this->eventSubscriber = $eventSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sector', 'entity', array(
                'class' => 'App\IndustryBundle\Entity\Sector',
                'empty_value' => 'Choose a sector',
                'attr'=>array('class'=>'form-control'),
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('n')->orderBy('n.title', 'ASC');
                }
            ))
            ->add('speciality', 'entity', array(
                'class' => 'App\IndustryBundle\Entity\Speciality',
                'empty_value' => 'Choose a speciality',
                'attr'=>array('class'=>'form-control'),
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('n')->orderBy('n.title', 'ASC');
                }
            ))
            ->add('category', 'skill_category_autocomplete', array('label' => 'Skill'))
            ->add('proficiency', 'choice', array(
                'attr'=>array('class'=>'form-control'),
                'choices' => $this->getProficiencyChoices()
            ))
            ->add('experience', 'choice', array(
                'attr'=>array('class'=>'form-control'),
                'choices' => $this->getExperienceChoies()
            ))
            ->add('lastUsed', 'choice', array(
                'label' => 'Last Used',
                'attr'=>array('class'=>'form-control'),
                'choices' => $this->getLastUsedChoices()
            ))
            ->add('interest', 'choice', array(
                'attr'=>array('class'=>'form-control'),
                'choices' => $this->getInterestChoices()
            ))
            ;

        if ($this->eventSubscriber) {
            $builder->addEventSubscriber($this->eventSubscriber);
        }
    }

    protected function getProficiencyChoices()
    {
        $choices = array('None', 'Beginner', 'Intermediate', 'Advanced', 'Expert');
        return array_combine($choices, $choices);
    }

    protected function getExperienceChoies()
    {
        $choices = array('None', 'Less than a year', '1-3 years', '3-5 years', '5+ years');
        return array_combine($choices, $choices);
    }

    protected function getLastUsedChoices()
    {
        $choices = array('Never', 'Current', 'Last year', '1-3 years ago', '3-5 years ago', '5+ years ago');
        return array_combine($choices, $choices);
    }

    protected function getInterestChoices()
    {
        $choices = array('None', 'Low', 'Medium', 'High');
        return array_combine($choices, $choices);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\SkillBundle\Entity\Skill'
        ));
    }

    public function getName()
    {
        return 'skill_form';
    }
}