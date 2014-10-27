<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 17.01.14
 * Time: 05:01
 */

namespace App\StatusBundle\Utils;


use App\StatusBundle\Entity\Group;
use App\StatusBundle\Entity\Status;
use Doctrine\Common\Cache\PhpFileCache;
use Doctrine\ORM\EntityManager;

class StatusTranslator
{
    /**
     * @var \Doctrine\Common\Cache\PhpFileCache
     */
    protected $file_cache;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entity_manager;

    public function __construct($file, EntityManager $entity_manager)
    {
        $this->file_cache = new PhpFileCache($file);
        $this->entity_manager = $entity_manager;
    }

    public function getStatusInfo($className, $status)
    {
        $statuses = $this->getStatuses($className);

        if(isset($statuses[$status])){
            return $statuses[$status];
        } else {
            throw new \InvalidArgumentException('Status "'.$status.'" doesn\'t exinst in group based on class named "'.$className.'"');
        }
    }

    public function getStatuses($className)
    {
        if(!$this->file_cache->contains($className)){
            $this->loadStatusGroup($className);
        }

        return $this->file_cache->fetch($className);
    }

    public function getStatusesNames($className)
    {
        $statuses = $this->getStatuses($className);

        $statuses_names = [];
        foreach($statuses as $key => $status){
            $statuses_names[$key] = $status['name'];
        }

        return $statuses_names;
    }

    protected function loadStatusGroup($className)
    {
        /** @var Group $statuses_group */
        $statuses_group = $this->entity_manager->getRepository('AppStatusBundle:Group')->getByClassName($className);

        if($statuses_group === null){
            throw new \InvalidArgumentException('Statuses group based on class named "'.$className.'" doesn\'t exist');
        }

        $statuses = [];
        foreach($statuses_group->getStatuses() as $status){
            /** @var Status $status */
            $statuses[$status->getValue()] = [
                'name' => $status->getName(),
                'color' => '#'.$status->getColor(),
                'description' => $status->getDescription()
            ];
        }

        $this->file_cache->save($className, $statuses);
    }

    public function clearCache()
    {
        $this->file_cache->deleteAll();
    }
} 