<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 19.03.14
 * Time: 12:24
 */

namespace App\FormBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

class IntervalTransformer implements DataTransformerInterface
{
    protected $workingHours;

    public function __construct($workingHours)
    {
        $this->workingHours = $workingHours;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return array
     */
    public function transform($value)
    {
        $days = floor($value/$this->workingHours);
        $hours = $value%$this->workingHours;
        $minutes = fmod($value, 1);

        return [
            'days' => $days,
            'hours' => $hours + $minutes
        ];
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return integer
     */
    public function reverseTransform($value)
    {
        $hours = intval($value['days'])*$this->workingHours+$value['hours'];
        return $hours > 0 ? $hours : null;
    }

} 