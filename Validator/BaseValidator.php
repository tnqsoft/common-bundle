<?php

namespace TNQSoft\CommonBundle\Validator;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints\Collection;

abstract class BaseValidator
{
    /**
     * @var RecursiveValidator
     */
    protected $validator;

    /**
     * @var array
     */
    protected $errorList;

    /**
     * @var boolean
     */
    protected $allowExtraFields = true;

    /**
     * @var string
     */
    protected $extraFieldsMessage = '';

    /**
     * @var string
     */
    protected $missingFieldsMessage = '';

    /**
     * @var array
     */
    protected $dataInput;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->errorList = array();
    }

    /**
    * Set Validator
    *
    * @param RecursiveValidator $validator
    */
    public function setValidator(RecursiveValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get error list
     *
     * @return array
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    /**
     * Validate
     *
     * @param array $input
     * @return boolean
     */
    public function validate($input)
    {
        $this->dataInput = $input;

        $this->errorList = array();

        //Get Validation collection
        $collections = $this->getValidatorCollection();
        if(empty($collections)) {
            return true;
        }

        //Create constraint
        $constraintCollection = array(
            'fields' => $collections,
            'allowExtraFields' => $this->allowExtraFields,
        );
        if($this->extraFieldsMessage !== '') {
            $constraintCollection['extraFieldsMessage'] = $this->extraFieldsMessage;
        }
        if($this->missingFieldsMessage !== '') {
            $constraintCollection['missingFieldsMessage'] = $this->missingFieldsMessage;
        }
        $constraint = new Collection($constraintCollection);

        //Validate
        $validatorResult = $this->validator->validate($input, $constraint);

        //Parse error
        $this->parseError($validatorResult);

        return (count($this->errorList) === 0);
    }

    /**
     * Get validator collection
     *
     * @return array
     */
    abstract public function getValidatorCollection();

    /**
     * Parse error
     *
     * @param  ConstraintViolationList $validatorResult
     * @return void
     */
    private function parseError(ConstraintViolationList $validatorResult)
    {
        foreach ($validatorResult as $error) {
            $path = $error->getPropertyPath();
            $path = preg_replace("/\]\[/", "/", $path);
            $path = preg_replace("/\[|\]/", "", $path);
            $path .= '/'.$error->getMessage();
            $pathArray = $this->path2NestedArray($path);
            $this->errorList = array_merge_recursive($this->errorList, $pathArray);
        }
    }

    /**
     * Path to nested array
     *
     * @param  string $path
     * @param  string $delimiter
     * @return array
     */
    private function path2NestedArray($path, $delimiter = '/')
    {
        $parts = explode($delimiter,$path);
        $arr = array_pop($parts);
        while ($bottom = array_pop($parts)) {
            $arr = array($bottom => $arr);
        }

        return $arr;
    }
}
