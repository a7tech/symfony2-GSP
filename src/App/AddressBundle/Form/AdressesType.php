<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 17.06.14
 * Time: 12:06
 */

namespace App\AddressBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdressesType extends AbstractType
{
    protected $entityManager;

    function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!empty($data)) {
                    if (count($data) == 1) {
                        $data[key($data)]['isMain'] = '1';
                    } else {
                        $mainFound = false;
                        foreach ($data as $d) {
                            if (isset($d['isMain'])) {
                                $mainFound = true;
                                break;
                            }
                        }

                        if (!$mainFound) {
                            $event->getForm()->addError(new FormError('please_select_main_address'));
                        }
                    }
                    $event->setData($data);
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'type'          => 'address_form',
            'allow_add'     => true,
            'allow_delete'  => true,
            'required'      => false,
            'error_bubbling'=> false,
            'attr'          => [
                'class' => 'horizontal-form addresses-collection'
            ]
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'backend_addresses';
    }


    public function getParent()
    {
        return 'collection';
    }
} 