<?php

namespace TNQSoft\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompareWithField extends Constraint
{
    public $field;

    public $operator;

    public $message = 'Giá trị của %field1% không thỏa mãn %operator% %field2%';
}
