<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-07-10
 * Time: 16:14
 */

namespace App\FormBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\Request;

class CommaToDotTransformer implements DataTransformerInterface
{
    protected $locale;

    /**
     * @var Request
     */
    protected $request;

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    protected function getLocale()
    {
        return $this->request !== null ? $this->request->getLocale() : 'en';
    }


    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        $locale = $this->getLocale();

        if($locale == 'fr'){
            $value = str_replace(',', '.', $value);
        }

        return $value;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        $locale = $this->getLocale();

        if($locale == 'fr'){
            $value = str_replace('.', ',', $value);
        }

        return $value;
    }

} 