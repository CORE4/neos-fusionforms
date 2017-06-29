<?php

namespace CORE4\Neos\FusionForms\Domain\Service;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core as Form;

/**
 * The form context
 * @Flow\Scope("singleton")
 */
class FormContext
{
    /**
     * @var Form\Model\FormDefinition
     */
    protected $form;

    /**
     * @var Form\Model\Page
     */
    protected $page;

    /**
     * @var Form\RunTime\FormRuntime
     */
    protected $runtime;


    /**
     * Initializes the form context
     *
     * This is considered safe since forms are not supposed to be rendered in a nested way
     *
     * @param Form\Model\FormDefinition $form
     */
    public function initializeForm(Form\Model\FormDefinition $form)
    {
        $this->form = $form;
    }

    /**
     * Initializes the form context
     *
     * This is considered safe since pages are not supposed to be rendered in a nested way
     *
     * @param Form\Model\Page $page
     */
    public function initializePage(Form\Model\Page $page)
    {
        $this->page = $page;
    }

    public function initializeRuntime(Form\RunTime\FormRuntime $runtime)
    {
        $this->runtime = $runtime;
    }


    public function getForm(): Form\Model\FormDefinition
    {
        return $this->form;
    }

    public function getPage(): Form\Model\Page
    {
        return $this->page;
    }

    public function getRuntime(): Form\RunTime\FormRuntime
    {
        return $this->runtime;
    }

    public function hasRuntime(): bool
    {
        return $this->runtime instanceof Form\RunTime\FormRuntime;
    }
}