<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 19.02.14
 * Time: 11:03
 */

namespace App\ProjectBundle\Form\Type;


use App\CoreBundle\Entity\EntityRepository;
use App\ProjectBundle\Entity\Project;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectChoiceType extends AbstractType
{
    protected $entity_manager;

    protected $projects;

    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $projects_type = [];
        foreach($this->projects as $project){
            /** @var Project $project */
            $projects_type[$project->getId()] = $project->getType();
        }

        $view->vars['attr']['data-types'] = json_encode($projects_type);
        $view->vars['attr']['data-draft'] = Project::TYPE_ESTIMATE;
        $view->vars['attr']['data-project'] = Project::TYPE_PROJECT;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $class = 'AppProjectBundle:Project';
        $this->projects = $this->entity_manager->getRepository($class)->getAll();

        $resolver->setDefaults([
            'class'=> $class,
            'choices' => $this->projects
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'project_choice';
    }


    public function getParent()
    {
        return 'entity';
    }

} 