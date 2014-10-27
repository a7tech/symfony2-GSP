<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 15.01.14
 * Time: 18:08
 */

namespace App\StatusBundle\Command;

use App\StatusBundle\Entity\Group;
use App\StatusBundle\Entity\Status;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:statuses:create')
            ->setDescription('Create statues entity representation');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //get Classes to synchronize
        $classes = $this->getContainer()->getParameter('statuses.classes');

        $groups = [];
        foreach($classes as $class_name){
            $class_reflection = new \ReflectionClass($class_name);
            if(!$class_reflection->implementsInterface('App\StatusBundle\Entity\StatusesProviderInterface')){
                throw new \InvalidArgumentException($class_name.' doesn\'t implement interface StatusesProviderInterface');
            }

            $class_groups = $class_name::getStatusesArray();

            foreach($class_groups as $group_name => $group_statuses){
                //uniqueness check
                if(!isset($groups[$group_name])){
                    $groups[$group_name] = [
                        'class' => $class_name,
                        'statuses' => $group_statuses
                    ];
                } else {
                    throw new \LogicException('Group with name "'.$group_name.'" is duplicated');
                }
            }
        }

        $entity_manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $status_groups_repository  = $entity_manager->getRepository('AppStatusBundle:Group');
        $groups_entities = [];
        foreach($status_groups_repository->findAll() as $group_entity){
            /** @var Group $group_entity */
            $groups_entities[$group_entity->getClassName()] = $group_entity;
        }

        foreach($groups as $group => $data){
            if(isset($groups_entities[$group])){
                $group_entity = $groups_entities[$group];
            } else {
                $group_entity = new Group($data['class'], $group);
            }

            $statuses = [];
            foreach($group_entity->getStatuses() as $status){
                /** @var Status $status */
                if(!isset($data['statuses'][$status->getValue()])){
                    //remove unneeded status
                    $entity_manager->remove($status);
                } else {
                    $statuses[$status->getValue()] = $status;
                }

            }

            //statuses to save, use new array to create correct order
            $new_statuses = [];
            foreach($data['statuses'] as $value => $name){
                if(isset($statuses[$value])){
                    $status = $statuses[$value];
                } else {
                    $status = new Status($name, $value);
                }

                $new_statuses[] = $status;
            }

            $group_entity->setStatuses($new_statuses);
            $entity_manager->persist($group_entity);
            $output->writeln('Group "'.$group.'" updated');
        }

        //clear unneeded groups
        foreach($groups_entities as $group_entity_check){
            /** @var Group $group_entity_check */
            if(!isset($groups[$group_entity_check->getClassName()])){
                $entity_manager->remove($group_entity_check);
            }
        }

        $entity_manager->flush();
    }


} 