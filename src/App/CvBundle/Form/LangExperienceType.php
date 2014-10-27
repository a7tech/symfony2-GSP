<?php
/**
 * LangExperienceType
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 22.08.13 18:21
 */

namespace App\CvBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LangExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('language', 'entity', array(
                'empty_value' => 'Choose a language',
                'class' => 'AppLanguageBundle:Language',
                'attr' => array('class' => 'form-control'),
                'query_builder'=>function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('n')->orderBy('n.name', 'ASC');
                }
            ))
            ->add('read', 'checkbox', array('required' => false))
            ->add('write', 'checkbox', array('required' => false))
            ->add('speak', 'checkbox', array('required' => false))
            ->add('proficiency', 'choice', array(
                'required' => false,
                'empty_value' => 'Select an option',
                'attr' => array('class' => 'form-control'),
                'choices' => $this->getProficiencyChoices()
            ))
            ->add('experience', 'choice', array(
                'required' => false,
                'empty_value' => 'Select an option',
                'attr' => array('class' => 'form-control'),
                'choices' => $this->getExperienceChoies()
            ))
            ->add('lastUsed', 'choice', array(
                'required' => false,
                'empty_value' => 'Select an option',
                'attr' => array('class' => 'form-control'),
                'choices' => $this->getLastUsedChoices()
            ))
            ->add('interest', 'choice', array(
                'required' => false,
                'empty_value' => 'Select an option',
                'attr' => array('class' => 'form-control'),
                'choices' => $this->getInterestChoices()
            ))
            ->add('description', 'textarea', array(
                'label' => 'Comments',
                'required' => false
            ))
        ;
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
            'data_class' => 'App\CvBundle\Entity\LangExperience'
        ));
    }

    public function getName()
    {
        return 'lang_experience_form';
    }
}