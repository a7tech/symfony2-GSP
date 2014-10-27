<?php
/**
 * TreeWrapper
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 04.08.13 21:38
 */

namespace App\CategoryBundle\NestedSet;


class TreeWrapper
{
    /**
     * @var array
     */
    protected $tree;

    /**
     * @var array
     */
    protected $treeByLevel;

    /**
     * __construct
     *
     * @param array $tree
     */
    public function __construct(array $tree)
    {
        $this->tree = $tree;
        $this->treeByLevel = $this->combineTreeByLevel($tree);
    }

    /**
     * combineTreeByLevel
     *
     * @param array $tree
     * @return array
     */
    protected function combineTreeByLevel(array $tree)
    {
        $treeByLevel = array();

        /** @var NodeInterface $node */
        foreach($tree as $node) {
            $lvl = $node->getLevel();
            if (isset($treeByLevel[$lvl])) {
                $treeByLevel[$lvl][] = $node;
            } else {
                $treeByLevel[$lvl] = array($node);
            }
        }

        return $treeByLevel;
    }

    /**
     * hasPrevSibling
     *
     * @param NodeInterface $node
     * @return bool
     */
    public function hasPrevSibling(NodeInterface $node)
    {
        return $this->getPrevSibling($node) !== null;
    }

    /**
     * hasNextSibling
     *
     * @param NodeInterface $node
     * @return bool
     */
    public function hasNextSibling(NodeInterface $node)
    {
        return $this->getNextSibling($node) !== null;
    }

    /**
     * getPrevSibling
     *
     * @param NodeInterface $node
     * @return NodeInterface|null
     */
    public function getPrevSibling(NodeInterface $node)
    {
        $lvl = $node->getLevel();

        if (!empty($this->treeByLevel[$lvl])) {
            $siblings = $this->treeByLevel[$lvl];
            $index = $this->findSiblingsIndex($siblings, $node);
            if ($index > 0) {
                return $siblings[$index-1];
            }
        }

        return null;
    }

    /**
     * getNextSibling
     *
     * @param NodeInterface $node
     * @return NodeInterface|null
     */
    public function getNextSibling(NodeInterface $node)
    {
        $lvl = $node->getLevel();

        if (!empty($this->treeByLevel[$lvl])) {
            $siblings = $this->treeByLevel[$lvl];
            $index = $this->findSiblingsIndex($siblings, $node);
            if ($index >= 0 && ($index + 1) < count($siblings)) {
                return $siblings[$index+1];
            }
        }

        return null;
    }

    /**
     * findSiblingsIndex
     *
     * @param array $siblings
     * @param NodeInterface $needle
     * @return int
     */
    protected function findSiblingsIndex(array $siblings, NodeInterface $needle)
    {
        /** @var NodeInterface $node */
        foreach ($siblings as $index => $node) {
            if ($node === $needle) {
                return $index;
            }
        }

        return -1;
    }
}