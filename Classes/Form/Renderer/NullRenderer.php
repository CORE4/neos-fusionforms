<?php

namespace CORE4\Neos\FusionForms\Form\Renderer;

/*
 * This file is part of the CORE4.Neos.FusionForms package.
 */
use Neos\Flow\Annotations as Flow;
use Neos\Form\Core as Form;
use Neos\Flow\Mvc;

/**
 * Null renderer
 *
 * Useful for triggering the form runtime rendering without producing any output
 */
class NullRenderer implements Form\Renderer\RendererInterface
{
    public function setControllerContext(Mvc\Controller\ControllerContext $controllerContext)
    {
    }

    public function renderRenderable(Form\Model\Renderable\RootRenderableInterface $renderable)
    {
        return '';
    }

    public function setFormRuntime(Form\Runtime\FormRuntime $formRuntime)
    {
    }

    public function getFormRuntime()
    {
    }
}
