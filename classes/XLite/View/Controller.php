<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Controller main widget
 */
class Controller extends \XLite\View\AView
{
    /**
     * Content of the currnt page
     * NOTE: this is a text, so it's not passed by reference; do not wrap it into a getter (or pass by reference)
     * NOTE: until it's not accessing via the function, do not change its access modifier
     *
     * @var string
     */
    public static $bodyContent = null;

    /**
     * Get html tag prefixes
     *
     * @return array
     */
    public static function defineHTMLPrefixes()
    {
        return [];
    }

    /**
     * @param array  $params          Widget params OPTIONAL
     * @param string $contentTemplate Central area template OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [], $contentTemplate = null)
    {
        parent::__construct($params);

        $this->template = $contentTemplate;
    }

    /**
     * Show current page and, optionally, footer
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        if (!$this->isSilent()) {
            $this->displayPage($template);
        }

        $this->postprocess();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->isAJAXCenterRequest() ? 'layout/content/center_top.twig' : 'body.twig';
    }

    /**
     * Get body class
     *
     * @return string
     */
    protected function getBodyClass()
    {
        return implode(' ', $this->defineBodyClasses());
    }

    /**
     * Get authorization class
     *
     * @return string
     */
    protected function getAuthClass()
    {
        $class = 'unauthorized';

        if (
            \XLite\Core\Auth::getInstance()->isLogged()
            && (
                !\XLite::isAdminZone()
                || (\XLite\Core\Auth::getInstance()->getProfile()->isAdmin()
                && !$this->isForceChangePassword())
            )
        ) {
            $class = 'authorized';
        }

        return $class;
    }

    /**
     * The layout defines the specific CSS classes for the 'body' tag
     * The body CSS classes can define:
     *
     * AREA: area-a / area-c
     * SKINS that are added to this interface: skin-<skin1>, skin-<skin2>, ...
     * TARGET : target-<target_name>
     * Sidebars: one-sidebar | two-sidebars | no-sidebars | sidebar-first | sidebar-second
     *
     * @return array Array of CSS classes to apply to the 'body' tag
     */
    protected function defineBodyClasses()
    {
        $classes = [
            'area-' . (\XLite::isAdminZone() ? 'a' : 'c'),
        ];

        $classes[] = $this->getAuthClass();

        $classes[] = 'target-' . str_replace('_', '-', \XLite\Core\Request::getInstance()->target);

        $first = \XLite\Core\Layout::getInstance()->isSidebarFirstVisible();
        $second = \XLite\Core\Layout::getInstance()->isSidebarSecondVisible();

        if ($first && $second) {
            $classes[] = 'two-sidebars';
        } elseif ($first || $second) {
            $classes[] = 'one-sidebar';
        } else {
            $classes[] = 'no-sidebars';
        }

        $sidebarState = \XLite\Core\Layout::getInstance()->getSidebarState();

        if ($first) {
            $classes[] = 'sidebar-first';
            if ($sidebarState & \XLite\Core\Layout::SIDEBAR_STATE_FIRST_EMPTY) {
                $classes[] = 'sidebar-first-empty';
            }

            if ($sidebarState & \XLite\Core\Layout::SIDEBAR_STATE_FIRST_ONLY_CATEGORIES) {
                $classes[] = 'sidebar-first-only-categories';
            }
        }

        if ($second) {
            $classes[] = 'sidebar-second';
            if ($sidebarState & \XLite\Core\Layout::SIDEBAR_STATE_SECOND_EMPTY) {
                $classes[] = 'sidebar-second-empty';
            }

            if ($sidebarState & \XLite\Core\Layout::SIDEBAR_STATE_SECOND_ONLY_CATEGORIES) {
                $classes[] = 'sidebar-second-only-categories';
            }
        }

        $classes = \XLite::getController()->defineBodyClasses($classes);

        return $classes;
    }

    /**
     * Before using the CSS class in the 'class' attribute it must be prepared to be valid
     * The restricted symbols are changed to '-'
     *
     * @param string $class CSS class name to be prepared
     *
     * @return string
     *
     * @see \XLite\View\AView::defineBodyClasses()
     */
    protected function prepareCSSClass($class)
    {
        return preg_replace('/[^a-z0-9_-]+/Si', '-', $class);
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SILENT => new \XLite\Model\WidgetParam\TypeBool('Silent', false),
        ];
    }

    /**
     * @return boolean
     */
    protected function isSilent()
    {
        return $this->getParam(self::PARAM_SILENT);
    }

    /**
     * @return \XLite\View\AView
     */
    protected function getContentWidget()
    {
        return $this->getWidget([\XLite\View\AView::PARAM_TEMPLATE => $this->template], '\XLite\View\Content');
    }

    /**
     * @return void
     */
    protected function prepareContent()
    {
        self::$bodyContent = $this->getContentWidget()->getContent();
    }

    /**
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    protected function displayPage($template = null)
    {
        $this->prepareContent();

        parent::display($template);
    }

    /**
     * @return array
     */
    protected function getCommonJSData()
    {
        return $this->defineCommonJSData();
    }

    /**
     * Get html tag attributes
     *
     * @return array
     */
    protected function getHTMLAttributes()
    {
        $list = [];

        $prefixes = static::defineHTMLPrefixes();
        if ($prefixes) {
            $data = [];
            foreach ($prefixes as $name => $uri) {
                $data[] = $name . ': ' . $uri;
            }
            $prefixes = implode(' ', $data);
        }

        if ($prefixes) {
            $list['prefix'] = $prefixes;
        }

        return $list;
    }
}
