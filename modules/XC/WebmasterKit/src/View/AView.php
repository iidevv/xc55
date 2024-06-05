<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\View;

use XCart\Extender\Mapping\Extender;
use XC\WebmasterKit\Core\TemplatesDebugger;

/**
 * Abstract widget
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    /**
     * Prepare template display
     *
     * @param string $template Template short path
     *
     * @return array
     */
    protected function prepareTemplateDisplay($template)
    {
        $result = parent::prepareTemplateDisplay($template);

        if (TemplatesDebugger::getInstance()->isMarkTemplatesEnabled()) {
            TemplatesDebugger::getInstance()->increaseCurrentTemplateId();

            $class       = get_class($this);
            $template    = substr($template, strlen(LC_DIR_SKINS));
            $templateId  = TemplatesDebugger::getInstance()->getCurrentTemplateId();
            $markTplText = "{$class} : {$template} ({$templateId})";

            if ($this->viewListName) {
                $markTplText .= " ['{$this->viewListName}' list child]";
            }

            echo("<!-- {$markTplText} {@! -->");

            $result['markTplText'] = $markTplText;
        }

        return $result;
    }

    /**
     * Finalize template display
     *
     * @param string $template     Template short path
     * @param array  $profilerData Profiler data which is calculated and returned in the 'prepareTemplateDisplay' method
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        if (isset($profilerData['markTplText'])) {
            echo ("<!-- !@} {$profilerData['markTplText']} -->");
        }

        parent::finalizeTemplateDisplay($template, $profilerData);
    }
}
