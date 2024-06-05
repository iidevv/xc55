<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig;

use Twig\Error\Error;
use XLite\Core\Exception\CallToAMethodOnNonObject;
use XLite\Core\Exception\FatalException;
use XLite\Core\Exception\MethodNotFound;

/**
 * Base class for compiled templates.
 *
 * TODO: Move widget instantiation logic from AView to a separate WidgetFactory
 */
abstract class Template extends \Twig\Template
{
    protected static $cache = [];

    protected function displayWithErrorHandling(array $context, array $blocks = [])
    {
        try {
            $this->doDisplay($context, $blocks);
        } catch (Error $e) {
            // this is mostly useful for Twig\Error\LoaderError exceptions
            // see Twig\Error\LoaderError
            if ($e->getTemplateLine() === false) {
                $e->setTemplateLine(-1);
                $e->guess();
            }

            $this->logTwigError($e);
        }

        // Do not catch non-Twig exceptions

        /*catch (Exception $e) {
            throw new Twig_Error_Runtime(sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, $this->getTemplateName(), $e);
        }*/
    }

    /**
     * @param Error $e
     */
    protected function logTwigError(Error $e)
    {
        $template = $this->getSourceContext()->getPath() ?: $this->getTemplateName();

        throw new FatalException(sprintf(
            "Twig error (see details below) \nDescription: %s \nTemplate: %s \nLine: %s",
            $e->getMessage(),
            $template,
            $e->getTemplateLine()
        ));
    }

    /**
     * @param MethodNotFound $e
     */
    protected function logMethodNotFoundInTemplate(MethodNotFound $e, $object)
    {
        $template = $this->getSourceContext()->getPath() ?: $this->getTemplateName();

        throw new FatalException(sprintf(
            "Twig error (see details below) \nDescription: Trying to call undefined method. \nTemplate: %s \nFunction: %s \nObject - %s",
            $template,
            $e->getMethod(),
            get_class($object)
        ));
    }

    /**
     * @param \TypeError $e
     */
    protected function logMethodTypeErrorInTemplate(\TypeError $e)
    {
        $template = $this->getSourceContext()->getPath() ?: $this->getTemplateName();

        throw new FatalException(sprintf(
            "Twig error (see details below) \nDescription: %s \nTemplate: %s",
            $e->getMessage(),
            $template
        ));
    }

    /**
     * @param CallToAMethodOnNonObject $e
     */
    protected function logCallToMethodOnNonObjectInTemplate(CallToAMethodOnNonObject $e)
    {
        $template = $this->getSourceContext()->getPath() ?: $this->getTemplateName();

        throw new FatalException(sprintf(
            "Twig error (see details below) \nDescription: %s \nTemplate: %s",
            $e->getMessage(),
            $template
        ));
    }
}
