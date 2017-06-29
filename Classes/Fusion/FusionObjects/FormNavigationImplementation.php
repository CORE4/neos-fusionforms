<?php

namespace CORE4\Neos\FusionForms\Fusion\FusionObjects;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core as Form;
use Neos\Flow\Http;
use Neos\Form\Exception\PresetNotFoundException;
use Neos\Fusion\FusionObjects\Helpers\FluidView;
use Neos\Utility\Arrays;

/**
 * Fusion object rendering a form
 */
class FormNavigationImplementation extends FormTemplateImplementation
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

        $this->pages = $this->runtime->render($this->path . '/pages');

        return parent::evaluate();
    }


    protected function startFormRuntime(): Form\Runtime\FormRuntime
    {
        $response = new Http\Response($this->runtime->getControllerContext()->getResponse());
        $formRuntime = $this->getForm()->bind($this->runtime->getControllerContext()->getRequest(), $response);

        return $formRuntime;
    }


    protected function buildFormDefinition(): Form\Model\FormDefinition
    {
        $form = new Form\Model\FormDefinition(
            $this->fusionValue('identifier'),
            $this->getPresetConfiguration($this->fusionValue('preset') ?: 'default'),
            $this->fusionValue('type') ?: 'Neos.Form:Form'
        );

        return $form;
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
        $formRuntime = $this->startFormRuntime();
        $view->assignMultiple([
            'formRuntime' => $formRuntime,
            'currentPage' => $this->pages[$formRuntime->getCurrentPage()->getIndex()]
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
