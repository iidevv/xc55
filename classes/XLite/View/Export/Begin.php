<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Export;

/**
 * Begin section
 */
class Begin extends \XLite\View\RequestHandler\ARequestHandler
{
    public const PARAM_PRESELECT = 'preselect';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRESELECT => new \XLite\Model\WidgetParam\TypeString('Preselected class', 'XLite\Logic\Export\Step\Products'),
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_PRESELECT;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'export/begin.twig';
    }

    /**
     * Avoid using preselect from session
     *
     * @param string $param Parameter name
     *
     * @return mixed
     */
    protected function getSavedRequestParam($param)
    {
        $result = null;

        if ($param != static::PARAM_PRESELECT) {
            $result = parent::getSavedRequestParam($param);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getPreselectedClass()
    {
        return $this->getParam(self::PARAM_PRESELECT);
    }
}
