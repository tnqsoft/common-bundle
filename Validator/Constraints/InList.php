<?php

namespace TNQSoft\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class InList extends Constraint
{
    public $list;

    public $message = 'Giá trị %string% nhập không đúng';
}
