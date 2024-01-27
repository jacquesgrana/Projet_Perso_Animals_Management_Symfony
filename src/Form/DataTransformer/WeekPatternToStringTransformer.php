<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use App\Library\WeekPatternLibrary;

class WeekPatternToStringTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        // Convert the array to a string
        //return implode(', ', $value);
        return WeekPatternLibrary::getDayNames($value);
    }

    public function reverseTransform(mixed $value): mixed
    {
        // Convert the string back to an array
        //return explode(', ', $value);
        return WeekPatternLibrary::getWeekPattern($value);
    }
}

?>