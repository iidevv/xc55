<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\FormField\Label;

class Sendfile extends \XLite\View\FormField\Label
{
    protected function getDir()
    {
        return 'modules/CDev/Egoods/settings';
    }

    protected function isVisible()
    {
        return parent::isVisible() && !\Includes\Utils\ConfigParser::getOptions(['other', 'use_sendfile']);
    }

    protected function getFieldTemplate()
    {
        return 'sendfile.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/CDev/Egoods/settings/style.css'
        ]);
    }

    protected function getArticleUrl()
    {
        return static::t('https://support.x-cart.com/en/articles/5214111-large-file-download-performance');
    }
}
