<?php

namespace CORE4\Neos\FusionForms\Fusion\FusionObjects;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\Page;

/**
 * Fusion object rendering a form page
 */
class FormPageImplementation extends FormTemplateImplementation
{
    public function evaluate()
    {
        // @todo refactor me by removing the check once the rendering vs. validation race condition has been resolved
        $alreadyInserted = false;
        foreach ($this->getForm()->getPages() as $page) {
            /** @var Page $page */
            if ($page->getIdentifier() === $this->fusionValue('identifier')) {
                $alreadyInserted = true;
                break;
            }
        }
        if (!$alreadyInserted) {
            $page = new Page(
                $this->fusionValue('identifier'),
                $this->fusionValue('type') ?: 'Neos.Form:Page'
            );

            $this->getForm()->addPage($page);
            $this->formContext->initializePage($page);
        }

        return parent::evaluate();
    }
}