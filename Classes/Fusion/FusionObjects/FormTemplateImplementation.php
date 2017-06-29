<?php

namespace CORE4\Neos\FusionForms\Fusion\FusionObjects;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use CORE4\Neos\FusionForms\Domain\Service\FormContext;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\ViewHelpers\FormViewHelper;
use Neos\Form\Core as Form;
use Neos\Fusion\FusionObjects\Helpers;
use Neos\Fusion\FusionObjects\TemplateImplementation;

/**
 * Fusion object rendering a form template component
 */
class FormTemplateImplementation extends TemplateImplementation
{
    /**
     * @Flow\Inject
     * @var FormContext
     */
    protected $formContext;


    public function getForm(): Form\Model\FormDefinition
    {
        return $this->formContext->getForm();
    }

    public function getPage(): Form\Model\Page
    {
        return $this->formContext->getPage();
    }

    public function getFormRuntime(): Form\Runtime\FormRuntime
    {
        return $this->formContext->getRuntime();
    }


    protected function initializeView(Helpers\FluidView $view)
    {
        $view->getRenderingContext()->getViewHelperVariableContainer()->add(
            FormViewHelper::class,
            'fieldNamePrefix',
            '--' . $this->formContext->getForm()->getIdentifier()
        );
        parent::initializeView($view);
    }
}
