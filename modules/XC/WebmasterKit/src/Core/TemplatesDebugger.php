<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\Core;

use XLite\Core\Config;
use XLite\Core\Request;

class TemplatesDebugger extends \XLite\Base\Singleton
{
    /**
     * Mark templates flag
     *
     * @var bool|null
     */
    protected $isMarkTemplatesEnabled;

    /**
     * @var int
     */
    protected $currentTemplateId = 0;

    /**
     * Check - mark templates mode is enabled or not
     *
     * @return bool
     */
    public function isMarkTemplatesEnabled()
    {
        if ($this->isMarkTemplatesEnabled === null) {
            $this->isMarkTemplatesEnabled = $this->getMarkTemplatesFlag();
        }

        return (bool) $this->isMarkTemplatesEnabled;
    }

    /**
     * @return bool
     */
    protected function getMarkTemplatesFlag()
    {
        return Config::getInstance()->XC->WebmasterKit->markTemplates
            && Request::getInstance()->isGet();
    }

    /**
     * @return int
     */
    public function getCurrentTemplateId()
    {
        return $this->currentTemplateId;
    }

    public function increaseCurrentTemplateId()
    {
        $this->currentTemplateId++;
    }
}
