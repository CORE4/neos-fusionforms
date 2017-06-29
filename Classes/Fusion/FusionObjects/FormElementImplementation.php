<?php

namespace CORE4\Neos\FusionForms\Fusion\FusionObjects;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use Neos\Error\Messages\Result;
use Neos\Flow\Annotations as Flow;
use Neos\Form\FormElements\GenericFormElement;

/**
 * Fusion object rendering a form page
 */
class FormElementImplementation extends FormTemplateImplementation
{
    public function evaluate()
    {
        // @todo refactor me by removing the check once the rendering vs. validation race condition has been resolved
        if (!$this->getForm()->getElementByIdentifier($this->fusionValue('identifier'))) {
            $formElement = new GenericFormElement(
                $this->fusionValue('identifier'),
                'Neos.Form:FormField'
            );

            $this->getPage()->addElement($formElement);

            if ($this->fusionValue('dataType')) {
                $formElement->setDataType($this->fusionValue('dataType'));
            }

            foreach ($this->fusionValue('validators') as $validatorConfiguration) {
                $formElement->createValidator($validatorConfiguration['identifier'], $validatorConfiguration['options']);
            }
        }
        return parent::evaluate();
    }

    public function getValidationResults()
    {
        if ($this->formContext->hasRuntime()) {
            $validationResult = $this->getFormRuntime()->getRequest()->getInternalArgument('__submittedArgumentValidationResults');
            if ($validationResult instanceof Result) {
                return $validationResult->forProperty($this->fusionValue('identifier'));
            }
        }
        return [];
    }

    public function getValue()
    {
        if ($this->formContext->hasRuntime()) {
            if (isset($this->formContext->getRuntime()[$this->fusionValue('identifier')])) {
                return $this->formContext->getRuntime()[$this->fusionValue('identifier')];
            }
            if (!empty($this->fusionValue('defaultValue'))) {
                return $this->fusionValue('defaultValue');
            }
        }

        return null;
    }
}