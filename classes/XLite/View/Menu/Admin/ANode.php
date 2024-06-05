<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use function is_scalar;

/**
 * Abstract menu node
 */
class ANode extends \XLite\View\AView implements \Serializable
{
    /**
     * Widget param names
     */
    public const PARAM_TITLE            = 'title';
    public const PARAM_TOOLTIP          = 'tooltip';
    public const PARAM_LINK             = 'link';
    public const PARAM_LIST             = 'list';
    public const PARAM_BLOCK            = 'block';
    public const PARAM_CLASS            = 'className';
    public const PARAM_TARGET           = 'linkTarget';
    public const PARAM_EXTRA            = 'extra';
    public const PARAM_PERMISSION       = 'permission';
    public const PARAM_PUBLIC_ACCESS    = 'publicAccess';
    public const PARAM_CHILDREN         = 'children';
    public const PARAM_SELECTED         = 'selected';
    public const PARAM_BLANK_PAGE       = 'blankPage';
    public const PARAM_ICON_FONT        = 'iconFont';
    public const PARAM_ICON_SVG         = 'iconSVG';
    public const PARAM_ICON_HTML        = 'iconHTML';
    public const PARAM_ICON_IMG         = 'iconIMG';
    public const PARAM_LABEL            = 'label';
    public const PARAM_SHOW_LABEL       = 'showLabel';
    public const PARAM_LABEL_LINK       = 'labelLink';
    public const PARAM_LABEL_TITLE      = 'labelTitle';
    public const PARAM_EXPANDED         = 'expanded';
    public const PARAM_DESCRIPTION      = 'description';
    public const PARAM_SELECTED_DECIDER = 'selectedDecider';
    public const PARAM_NAME             = 'name';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/node.twig';
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TITLE            => new \XLite\Model\WidgetParam\TypeString('Name', ''),
            static::PARAM_TOOLTIP          => new \XLite\Model\WidgetParam\TypeString('Tooltip', ''),
            static::PARAM_LINK             => new \XLite\Model\WidgetParam\TypeString('Link', ''),
            static::PARAM_BLOCK            => new \XLite\Model\WidgetParam\TypeString('Block', ''),
            static::PARAM_LIST             => new \XLite\Model\WidgetParam\TypeString('List', ''),
            static::PARAM_CLASS            => new \XLite\Model\WidgetParam\TypeString('Class name', ''),
            static::PARAM_TARGET           => new \XLite\Model\WidgetParam\TypeString('Target', ''),
            static::PARAM_EXTRA            => new \XLite\Model\WidgetParam\TypeCollection('Additional request params', []),
            static::PARAM_PERMISSION       => new \XLite\Model\WidgetParam\TypeString('Permission', ''),
            static::PARAM_PUBLIC_ACCESS    => new \XLite\Model\WidgetParam\TypeBool('Public access', false),
            static::PARAM_BLANK_PAGE       => new \XLite\Model\WidgetParam\TypeBool('Use blank page', false),
            static::PARAM_CHILDREN         => new \XLite\Model\WidgetParam\TypeCollection('Children', []),
            static::PARAM_SELECTED         => new \XLite\Model\WidgetParam\TypeBool('Selected', false),
            static::PARAM_ICON_FONT        => new \XLite\Model\WidgetParam\TypeString('Icon Awesome font name', ''),
            static::PARAM_ICON_SVG         => new \XLite\Model\WidgetParam\TypeString('Icon SVG image path', ''),
            static::PARAM_ICON_HTML        => new \XLite\Model\WidgetParam\TypeString('Icon HTML', ''),
            static::PARAM_ICON_IMG         => new \XLite\Model\WidgetParam\TypeString('Icon image path', ''),
            static::PARAM_LABEL            => new \XLite\Model\WidgetParam\TypeString('Label', ''),
            static::PARAM_SHOW_LABEL       => new \XLite\Model\WidgetParam\TypeBool('Show Label', true),
            static::PARAM_LABEL_LINK       => new \XLite\Model\WidgetParam\TypeString('Label link', ''),
            static::PARAM_LABEL_TITLE      => new \XLite\Model\WidgetParam\TypeString('Label title', ''),
            static::PARAM_EXPANDED         => new \XLite\Model\WidgetParam\TypeBool('Expanded', false),
            static::PARAM_SELECTED_DECIDER => new \XLite\Model\WidgetParam\TypeObject(
                'SelectedDecider',
                null,
                false,
                'XLite\View\Menu\Admin\SelectedDecider'
            ),
            static::PARAM_NAME             => new \XLite\Model\WidgetParam\TypeString('Name', ''),
            static::PARAM_DESCRIPTION      => new \XLite\Model\WidgetParam\TypeString('Description', ''),
        ];
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->getWidgetParams());
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->widgetParams = unserialize($serialized);
    }

    /**
     * Return blank page flag (target = "_blank" for the link)
     *
     * @return array
     */
    protected function getBlankPage()
    {
        return $this->getParam(static::PARAM_BLANK_PAGE);
    }

    /**
     * Return children
     *
     * @return array
     */
    protected function getChildren()
    {
        return $this->getParam(static::PARAM_CHILDREN);
    }

    /**
     * Check if submenu available for this item
     *
     * @return string
     */
    protected function hasChildren()
    {
        return (
                $this->getParam(static::PARAM_LIST) !== ''
                && 0 < strlen(trim($this->getViewListContent($this->getListName())))
            ) || $this->getChildren();
    }

    /**
     * Check - node is branch but has empty childs list
     *
     * @return bool
     */
    protected function isEmptyChildsList()
    {
        return $this->getParam(static::PARAM_LIST) !== ''
            && strlen(trim($this->getViewListContent($this->getListName()))) == 0
            && !$this->getChildren();
    }

    /**
     * @return SelectedDecider
     */
    protected function getSelectedDecider()
    {
        return $this->getParam(static::PARAM_SELECTED_DECIDER);
    }

    /**
     * Return node link
     *
     * @return string
     */
    protected function getLink()
    {
        $link = null;

        if ($this->getParam(static::PARAM_LINK) !== '') {
            $link = $this->getParam(static::PARAM_LINK);
        } elseif ($this->getNodeTarget() !== '') {
            $link = $this->buildURL($this->getNodeTarget(), '', $this->getParam(static::PARAM_EXTRA));
        }

        return $link;
    }

    /**
     * Return the block text
     *
     * @return string
     */
    protected function getBlock()
    {
        return $this->getParam(static::PARAM_BLOCK);
    }

    /**
     * Return if the the link should be active
     * (linked to a current page)
     *
     * @return bool
     */
    protected function isCurrentPageLink()
    {
        return $this->getParam(static::PARAM_SELECTED);
    }

    /**
     * Check - node is expanded or not
     *
     * @return bool
     */
    protected function isExpanded()
    {
        return $this->getParam(static::PARAM_EXPANDED)
            && $this->getParam(static::PARAM_CHILDREN);
    }

    /**
     * Get link template
     *
     * @return string
     */
    protected function getLinkTemplate()
    {
        return $this->getDir() . '/link.twig';
    }

    /**
     * Get contrainer tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return [
            'class' => trim('menu-item ' . $this->getCSSClass()),
        ];
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return $this->getParam(static::PARAM_NAME);
    }

    /**
     * Get content of the dynamic widget that renders css classes for menu item.
     *
     * @return string
     */
    public function getMenuItemClasses()
    {
        $widget = $this->getChildWidget('XLite\View\Menu\Admin\ExpandedMenuNodeClass', [
            ExpandedMenuNodeClass::PARAM_DECIDER => $this->getSelectedDecider(),
            ExpandedMenuNodeClass::PARAM_NAME    => $this->getName(),
        ]);

        return $widget->getContent();
    }

    /**
     * Return CSS class for the link item
     *
     * @return string
     */
    protected function getCSSClass()
    {
        $class = $this->getParam(static::PARAM_CLASS);

        $class .= $this->getIcon() ? ' icon' : ' no-icon';

        $class .= ' ' . $this->getMenuItemClasses();

        if ($this->getLabel()) {
            $class .= ' has-label';
        }

        if (count($this->getChildren()) === 0) {
            $class .= ' empty';
        }

        return trim($class);
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isEmptyChildsList();
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function checkACL()
    {
        if (!parent::checkACL()) {
            return false;
        }

        if (
            $this->getParam(static::PARAM_LIST)
            || $this->getParam(static::PARAM_PUBLIC_ACCESS)
        ) {
            return true;
        }

        $auth = \XLite\Core\Auth::getInstance();

        if ($auth->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)) {
            return true;
        }

        $additionalPermission = $this->getParam(static::PARAM_PERMISSION);
        if (is_scalar($additionalPermission)) {
            $additionalPermission = [$additionalPermission];
        }

        foreach ($additionalPermission as $permission) {
            if ($auth->isPermissionAllowed($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->getParam(static::PARAM_TITLE);
    }

    /**
     * Get tooltip
     *
     * @return string
     */
    protected function getTooltip()
    {
        return $this->getParam(static::PARAM_TOOLTIP) ?: $this->getTitle();
    }

    /**
     * Get icon
     *
     * @return string
     */
    protected function getIcon()
    {
        if ($this->getParam(static::PARAM_ICON_FONT)) {
            $result = '<i class="fa ' . $this->getParam(static::PARAM_ICON_FONT) . '"></i>';
        } elseif ($this->getParam(static::PARAM_ICON_SVG)) {
            $result = $this->getSVGImage($this->getParam(static::PARAM_ICON_SVG));
        } elseif ($this->getParam(static::PARAM_ICON_HTML)) {
            $result = $this->getParam(static::PARAM_ICON_HTML);
        } elseif ($this->getParam(static::PARAM_ICON_IMG)) {
            $result = $this->getImageTag($this->getParam(static::PARAM_ICON_IMG));
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Get image tag
     *
     * @param string $src Image src
     *
     * @return string
     */
    protected function getImageTag($src)
    {
        $result = null;

        if ($src) {
            $result = sprintf('<img src="%s" alt="" />', $src);
        }

        return $result;
    }

    /**
     * Get label
     *
     * @return string
     */
    protected function getLabel()
    {
        return $this->getParam(static::PARAM_LABEL);
    }

    /**
     * Show label
     * Useful if only the circle indicator is needed
     *
     * @return bool
     */
    protected function showLabel()
    {
        return $this->getParam(static::PARAM_SHOW_LABEL);
    }

    /**
     * Get label link
     *
     * @return string
     */
    protected function getLabelLink()
    {
        return $this->getParam(static::PARAM_LABEL_LINK);
    }

    /**
     * Get label title
     *
     * @return string
     */
    protected function getLabelTitle()
    {
        return $this->getParam(static::PARAM_LABEL_TITLE);
    }

    /**
     * Get link target
     *
     * @return string
     */
    protected function getNodeTarget()
    {
        return $this->getParam(static::PARAM_TARGET);
    }

    /**
     * Get description
     *
     * @return string
     */
    protected function getDescription()
    {
        return $this->getParam(static::PARAM_DESCRIPTION);
    }
}
