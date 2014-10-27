<?php
/**
 * FormMapper
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 19.07.13 19:37
 */

namespace App\ProductBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class FormMapper
{
    /**
     * Form builder
     * @var FormBuilderInterface
     */
    private $builder;

    /**
     * Active group
     * @var mixed null|string
     */
    private $group = null;

    public function __construct(FormBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Add child to builder with group option
     */
    public function add($child, $type = null, array $options = array())
    {
        if (!array_key_exists('group', $options) and null !== $this->group) {
            $options['group'] = $this->group;
        }

        $this->builder->add($child, $type, $options);

        return $this;
    }

    /**
     * Set active group
     */
    public function with($group)
    {
        $this->group = $group;

        return $this;
    }
}