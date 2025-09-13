<?php

namespace Stormmore\Framework\Form;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Validation\Field;
use Stormmore\Framework\Validation\ValidationResult;
use Stormmore\Framework\Validation\Validator;

class Form
{
    public Errors $errors;

    public Request $request;
    public array $model;
    protected Validator $validator;
    private null|ValidationResult $validationResult;


    function __construct(Request $request)
    {
        $this->validationResult = new ValidationResult();
        $this->validator = new Validator();
        $this->request = $request;
        $this->errors = new Errors();
        $this->model = array();
    }

    function getValidator(): Validator
    {
        return $this->validator;
    }

    function validate(): ValidationResult
    {
        $this->validationResult = $this->validator->validate($this->request);
        $this->errors->setValidationResult($this->validationResult);
        return $this->validationResult;
    }

    public function add(Field $field): Form
    {
        $this->validator->add($field);
        return $this;
    }

    function setModel(array|object $model): Form
    {
        if (is_object($model)) {
            $model = get_object_vars($model);
        }
        $this->model = $model;

        return $this;
    }

    public function getValue(string $name): mixed
    {
        if ($this->request->has($name)) {
            return $this->request->get($name);
        }
        if ($this->model and array_key_exists($name, $this->model)) {
            return $this->model[$name];
        }
        return null;
    }

    public function __get(string $name): mixed
    {
        return $this->getValue($name);
    }

    function isValid(): bool
    {
        return $this->validationResult?->isValid() === true;
    }

    function isInvalid(): bool
    {
        return $this->validationResult != null and !$this->validationResult->isValid();
    }

    function isSubmittedSuccessfully(): bool
    {
        return $this->request->isPost() and $this->validate()->isValid();
    }
}