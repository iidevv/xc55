<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\MembershipsQuickData;

class Progress extends \XLite\View\AView
{
    use \XLite\View\EventTaskProgressProviderTrait;

    /**
     * Returns processing unit
     * @return mixed
     */
    protected function getProcessor()
    {
        return \XLite\Logic\MembershipsQuickData\Generator::getInstance();
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'memberships_quick_data';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/progress.twig';
    }
}
