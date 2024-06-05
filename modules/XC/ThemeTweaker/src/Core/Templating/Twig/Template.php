<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Templating\Twig;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Exception\CallToAMethodOnNonObject;
use XLite\Core\Exception\MethodNotFound;
use XC\ThemeTweaker\Controller\Admin\NotificationEditor;

/**
 * @Extender\Mixin
 */
abstract class Template extends \XLite\Core\Templating\Twig\Template
{
    protected function logMethodNotFoundInTemplate(MethodNotFound $e, $object)
    {
        if (!$this->isNotificationEditor()) {
            parent::logMethodNotFoundInTemplate($e, $object);
        } else {
            $this->setTemplateError();
        }
    }

    protected function logMethodTypeErrorInTemplate(\TypeError $e)
    {
        if (!$this->isNotificationEditor()) {
            parent::logMethodTypeErrorInTemplate($e);
        } else {
            $this->setTemplateError();
        }
    }

    protected function logCallToMethodOnNonObjectInTemplate(CallToAMethodOnNonObject $e)
    {
        if (!$this->isNotificationEditor()) {
            parent::logCallToMethodOnNonObjectInTemplate($e);
        } else {
            $this->setTemplateError();
        }
    }

    /**
     * @return bool
     */
    protected function isNotificationEditor()
    {
        return \XLite::getController() instanceof NotificationEditor;
    }

    /**
     * @see \XC\ThemeTweaker\Controller\Admin\NotificationEditor::addFailedTemplate
     */
    protected function setTemplateError()
    {
        \XLite::getController()->addFailedTemplate(
            $this->getSourceContext()->getPath()
        );
    }
}
