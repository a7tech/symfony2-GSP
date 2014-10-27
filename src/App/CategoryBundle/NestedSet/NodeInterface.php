<?php
/**
 * Node
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 04.08.13 21:41
 */

namespace App\CategoryBundle\NestedSet;


interface NodeInterface extends \DoctrineExtensions\NestedSet\Node
{
    /**
     * getLevel
     *
     * @return int
     */
    public function getLevel();
}