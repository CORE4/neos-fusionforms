<?php

namespace CORE4\Neos\FusionForms\ViewHelpers;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use CORE4\Neos\FusionForms\Domain\Service\FormContext;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\ViewHelpers\Form\AbstractFormViewHelper;
use Neos\FluidAdaptor\ViewHelpers\FormViewHelper;
use Neos\Form\Core\Model\Page;
use Neos\Form\FormElements\GenericFormElement;

/**
 * ViewHelper to add Fusion form variables to the current form
 */
class AddFusionFormVariablesViewHelper extends AbstractFormViewHelper
{
    /**
     * @Flow\Inject
     * @var FormContext
     */
    protected $formContext;


    public function render()
    {
        foreach ($this->formContext->getForm()->getPages() as $page) {
            /** @var Page $page */
            foreach ($page->getElementsRecursively() as $element) {
                /** @var GenericFormElement $element */
                $this->registerFieldNameForFormTokenGeneration($element->getIdentifier());
            }
        }
        $this->registerFieldNameForFormTokenGeneration('__currentPage');
        $this->viewHelperVariableContainer->add(FormViewHelper::class, 'fieldNamePrefix', '--' . $this->formContext->getForm()->getIdentifier());
    }
}
