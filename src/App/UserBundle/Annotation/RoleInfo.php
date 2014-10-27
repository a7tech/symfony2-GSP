<?php

/*
 * An annotation to store role information that can be parsed and stored in the database
 */

namespace App\UserBundle\Annotation;

use JMS\SecurityExtraBundle\Exception\InvalidArgumentException;

/**
 * Represents a @RoleInfo annotation.
 *
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class RoleInfo
{
    public $role;

    public $parent;

    public $desc;

    public $module;

    public function __construct(array $values)
    {
        if(isset($values['role'])){
            $this->role = $values['role'];
        }
        if(isset($values['module'])){
            $this->module = $values['module'];
        }
        if(isset($values['desc'])){
            $this->desc = $values['desc'];
        }
        if(isset($values['parent'])){
            $this->parent = $values['parent'];
        }
        if (!isset($values['role'])) {
            throw new InvalidArgumentException('You must define a "role" attribute for each RoleInfo annotation.');
        }
        if (!isset($values['module'])) {
            throw new InvalidArgumentException('You must define a "module" attribute for each RoleInfo annotation.');
        }
        if (!isset($values['parent'])) {
            throw new InvalidArgumentException('You must define a "parent" attribute for each RoleInfo annotation.');
        }
        if (!isset($values['desc'])) {
            throw new InvalidArgumentException('You must define a "desc" attribute for each RoleInfo annotation.');
        }
    }
}