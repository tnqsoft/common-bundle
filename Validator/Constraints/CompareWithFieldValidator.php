<?php

namespace TNQSoft\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\Util\PropertyPath;

class CompareWithFieldValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $form = $this->context->getRoot()->getData();
        $currentField = $this->context->getPropertyName();
        $compareField = $constraint->field;
        $method = 'get'.ucwords($compareField);

        if(!method_exists($form, $method)) {
            return false;
        }

        $compareValue = $form->$method();
        switch ($constraint->operator) {
            case '>':
                $check = $value > $compareValue;
                break;
            case '>=':
                $check = $value >= $compareValue;
                break;
            case '<':
                $check = $value < $compareValue;
                break;
            case '<=':
                $check = $value <= $compareValue;
                break;
            case '=':
            case '==':
                $check = $value == $compareValue;
                break;
            case '!':
            case '!=':
                $check = $value != $compareValue;
                break;
            default:
                $check = $value == $compareValue;
                break;
        }

        if($check === false) {
            $valueString = $value;
            if($value instanceof \DateTimeInterface) {
                $valueString = $value->format('Y-m-d H:i:s');
            }
            $this->context->addViolation(
                $constraint->message,
                array(
                    '%field1%' => $currentField,
                    '%field2%' => $compareField,
                    '%operator%' => $constraint->operator
                )
            );
        }
    }
}
