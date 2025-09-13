<?php

namespace Stormmore\Framework\Mvc;

use Stormmore\Framework\DependencyInjection\Resolver;
use Stormmore\Framework\FluentReflection\Class\FluentClass;
use Stormmore\Framework\FluentReflection\Class\FluentClassParameter;
use Stormmore\Framework\FluentReflection\Class\FluentClassParameters;
use Stormmore\Framework\FluentReflection\Shared\SafeValue;
use Stormmore\Framework\Mvc\Attributes\Bindable;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Mvc\IO\Request\RequestMapper;

class ControllerActionArguments
{
    private bool $initialized = false;
    private array $arguments = [];


    public function __construct(private readonly FluentClassParameters $parameters,
                                private readonly Request               $request,
                                private readonly Resolver              $resolver)
    {
    }

    public function areValid(): bool
    {
        $this->buildArguments();
        return count($this->arguments) == $this->parameters->count();
    }

    public function getArguments(): array
    {
        $this->buildArguments();
        return $this->arguments;
    }

    private function buildArguments(): void
    {
        if ($this->initialized) return;
        $this->initialized = true;

        $this->arguments = [];
        foreach ($this->parameters as $parameter) {
            $safeValue = $this->getArgumentForParameter($parameter);
            if (!$safeValue->exist) return;
            $this->arguments[] = $safeValue->value;
        }
    }

    private function getArgumentForParameter(FluentClassParameter $parameter): SafeValue
    {
        if ($parameter->type->hasUserDefinedTypes()) {
            return new SafeValue(true, $this->createClassObject($parameter));
        }

        $name = $parameter->getName();
        if ($this->request->has($name)) {
            $value = $this->request->getDefault($name);
            return $parameter->type->cast($value);
        }
        if ($parameter->type->hasDefaultValue()) {
            return new SafeValue(true, $parameter->type->getDefaultValue());
        }
        else if ($parameter->isNullable()) {
            return new SafeValue(true, null);
        }
        return new SafeValue(false, null);
    }

    private function createClassObject(FluentClassParameter $parameter): object
    {
        $classes = $parameter->type->getUserDefinedTypes();
        $fluentClass = FluentClass::create($classes[0]);
        $obj = $this->resolver->resolve($fluentClass);
        if ($fluentClass->hasAttribute(Bindable::class)) {
            RequestMapper::map($this->request, $obj);
        }
        return $obj;
    }
}