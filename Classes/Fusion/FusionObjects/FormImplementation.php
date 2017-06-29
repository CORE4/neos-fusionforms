<?php

namespace CORE4\Neos\FusionForms\Fusion\FusionObjects;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use CORE4\Neos\FusionForms\Form\Renderer\NullRenderer;
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\FormDefinition;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Flow\Http;
use Neos\Form\Exception\PresetNotFoundException;
use Neos\Fusion\FusionObjects\Helpers\FluidView;
use Neos\Utility\Arrays;

/**
 * Fusion object rendering a form
 */
class FormImplementation extends FormTemplateImplementation
{
    /**
     * @Flow\InjectConfiguration(package="Neos.Form")
     * @var array
     */
    protected $formSettings;

    /**
     * @var array
     */
    protected $pages;


    public function evaluate()
    {
        $this->formContext->initializeForm($this->buildFormDefinition());

        /** @todo check whether this fusion implementation can be used as a renderer */
        $this->pages = $this->runtime->render($this->path . '/pages');

        $formRuntime = $this->buildFormRuntime();
        $formRenderingResult = $formRuntime->render();
        if (is_null($formRuntime->getCurrentPage())) {
            // after last page, finishers 4tw
            return $formRenderingResult;
        }

        $this->formContext->initializeRuntime($formRuntime);

        // @todo refactor me once the rendering vs. validation race condition has been resolved
        $this->pages = $this->runtime->render($this->path . '/pages');

        return parent::evaluate();
    }


    protected function buildFormDefinition(): FormDefinition
    {
        $form = new FormDefinition(
            $this->fusionValue('identifier'),
            $this->getPresetConfiguration($this->fusionValue('preset') ?: 'default'),
            $this->fusionValue('type') ?: 'Neos.Form:Form'
        );
        $form->setRendererClassName(NullRenderer::class);
        foreach ($this->fusionValue('finishers') as $finisherConfiguration) {
            $form->createFinisher($finisherConfiguration['identifier'], $finisherConfiguration['options']);
        }

        return $form;
    }

    protected function buildFormRuntime(): FormRuntime
    {
        $response = new Http\Response($this->runtime->getControllerContext()->getResponse());
        $formRuntime = $this->getForm()->bind($this->runtime->getControllerContext()->getRequest(), $response);

        return $formRuntime;
    }


    /**
     * This is a template method which can be overridden in subclasses to add new variables which should
     * be available inside the Fluid template. It is needed e.g. for Expose.
     *
     * @param FluidView $view
     * @return void
     */
    protected function initializeView(FluidView $view)
    {
        $view->assignMultiple([
            'formRuntime' => $this->formContext->getRuntime(),
            'currentPage' => $this->pages[$this->formContext->getRuntime()->getCurrentPage()->getIndex()]
        ]);
    }

    /**
     * Get the preset configuration by $presetName, taking the preset hierarchy
     * (specified by *parentPreset*) into account.
     *
     * @param string $presetName name of the preset to get the configuration for
     * @return array the preset configuration
     * @throws PresetNotFoundException if preset with the name $presetName was not found
     * @api
     */
    public function getPresetConfiguration($presetName)
    {
        if (!isset($this->formSettings['presets'][$presetName])) {
            throw new PresetNotFoundException(sprintf('The Preset "%s" was not found underneath Neos: Form: presets.', $presetName), 1325685498);
        }
        $preset = $this->formSettings['presets'][$presetName];
        if (isset($preset['parentPreset'])) {
            $parentPreset = $this->getPresetConfiguration($preset['parentPreset']);
            unset($preset['parentPreset']);
            $preset = Arrays::arrayMergeRecursiveOverrule($parentPreset, $preset);
        }

        return $preset;
    }
}
