<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\InjectLoggerTrait;

/**
 * View list collection container
 */
class ListContainer extends \XLite\View\AView
{
    use InjectLoggerTrait;

    public const PARAM_INNER_TEMPLATE  = 'inner';
    public const PARAM_INNER_LIST      = 'innerList';
    public const PARAM_GROUP_NAME      = 'group';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_INNER_TEMPLATE => new \XLite\Model\WidgetParam\TypeFile('Template', ''),
            self::PARAM_INNER_LIST => new \XLite\Model\WidgetParam\TypeString('Inner List', $this->getDefaultInnerList()),
            self::PARAM_GROUP_NAME => new \XLite\Model\WidgetParam\TypeString('Group name', ''),
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultInnerList()
    {
        return '';
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getInnerTemplate()
    {
        return $this->getParam(self::PARAM_INNER_TEMPLATE);
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getInnerList()
    {
        return $this->getParam(self::PARAM_INNER_LIST);
    }

    /**
     * Return current template
     *
     * @return string
     */
    protected function getGroupName()
    {
        return $this->getParam(self::PARAM_GROUP_NAME);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'list_container.twig';
    }

    /**
     * Print widget inner content
     *
     * @return string
     */
    public function displayInnerContent()
    {
        return $this->getInnerContent();
    }

    /**
     * @return string
     */
    public function getInnerContent()
    {
        if ($this->getInnerList()) {
            return $this->getViewListContent($this->getInnerList());
        } elseif ($this->getInnerTemplate()) {
            $template = $this->getWidgetParams(self::PARAM_TEMPLATE);
            $template->setValue($this->getInnerTemplate());

            return $this->getContent();
        } else {
            $this->getLogger()->error('No list or template was given to ListContainer');
        }

        return '';
    }
}
