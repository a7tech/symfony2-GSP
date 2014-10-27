<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-07-11
 * Time: 14:33
 */

namespace App\FormBundle\Form\Extension;


use App\FormBundle\Form\DataTransformer\CommaToDotTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class PercentExtension extends AbstractTypeExtension
{
    protected $commaToDotTransformer;

    protected function getTransformer()
    {
        if($this->commaToDotTransformer === null) {
            $this->commaToDotTransformer = new CommaToDotTransformer();
        }

        return $this->commaToDotTransformer;
    }

    public function setRequest(Request $request = null)
    {
        $this->getTransformer()->setRequest($request);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->getTransformer());
    }


    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'percent';
    }

} 