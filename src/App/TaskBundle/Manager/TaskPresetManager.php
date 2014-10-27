<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 21.03.14
 * Time: 01:12
 */

namespace App\TaskBundle\Manager;


use App\TaskBundle\Entity\TaskPreset;

class TaskPresetManager extends TaskBaseManager
{
    public function create(TaskPreset $preset)
    {
        $this->normalizeOrdering($preset, true);
        $this->entityManager->persist($preset);
    }

    public function update(TaskPreset $preset)
    {
        $this->normalizeOrdering($preset, true);
        $this->entityManager->persist($preset);
    }

    public function remove(TaskPreset $preset)
    {
        $this->normalizeOrdering($preset, false);
        $this->entityManager->remove($preset);
    }

    protected function normalizeOrdering(TaskPreset $preset, $update = true)
    {
        $category = $preset->getCategory();
        $presets_repository = $this->entityManager->getRepository('AppTaskBundle:TaskPreset');
        $presets = $presets_repository->getByCategory($category);


        $this->normalizeItemsOrdering($preset, $presets, $update);
    }


} 