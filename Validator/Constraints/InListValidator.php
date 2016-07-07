<?php

namespace TNQSoft\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InListValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!in_array($value, $constraint->list)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}
