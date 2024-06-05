<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use Twig\Markup;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Abstract widget
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    use ExecuteCachedTrait;

    /**
     * Current templates tree
     *
     * @var \XLite\Core\CommonGraph
     */
    protected static $tree;

    /**
     * Current tree node
     *
     * @var \XLite\Core\CommonGraph
     */
    protected static $current;

    /**
     * Template id
     *
     * @var integer
     */
    protected static $templateId = 0;

    /**
     * Mark flag (null if not started)
     *
     * @var boolean|null
     */
    protected static $mark;

    /**
     * Allow mark
     *
     * @var boolean|null
     */
    protected static $allowMark;

    /**
     * @var string
     */
    protected $notificationRootTemplate;

    /**
     * So called "static constructor".
     * NOTE: do not call the "parent::__constructStatic()" explicitly: it will be called automatically
     *
     * @return void
     */
    public static function __constructStatic()
    {
        static::$tree    = new \XLite\Core\CommonGraph();
        static::$current = static::$tree;
    }

    /**
     * Returns current templates tree
     *
     * @return \XLite\Core\CommonGraph
     */
    public static function getTree()
    {
        return static::$tree;
    }

    /**
     * Returns current templates tree (HTML)
     *
     * @return string
     */
    public static function getHtmlTree()
    {
        return \XLite::isAdminZone()
            ? static::getAdminHtmlTree()
            : static::getCustomerHtmlTree();
    }

    /**
     * Returns current templates tree (HTML) (admin zone)
     *
     * @return string
     */
    protected static function getAdminHtmlTree()
    {
        return static::buildHtmlTreeNode(static::$tree);
    }

    /**
     * Returns current templates tree (HTML) (customer zone)
     *
     * @return string
     */
    protected static function getCustomerHtmlTree()
    {
        $htmlTree = static::buildHtmlTreeNode(static::$tree);

        if (!$htmlTree) {
            return '';
        }

        $result = '<div class="themeTweaker_tree not-processed" data-editor-tree data-interface="' . \XLite::INTERFACE_WEB . '"' . 'data-zone="' . \XLite::ZONE_CUSTOMER . '">';
        $result .= $htmlTree;
        $result .= '</div>';

        return $result;
    }

    /**
     * Returns current templates tree (HTML)
     *
     * @param \Includes\DataStructure\Graph $node Node
     *
     * @return string
     */
    public static function buildHtmlTreeNode(\Includes\DataStructure\Graph $node)
    {
        $result   = '';
        $children = $node->getChildren();

        if ($children) {
            $result = '<ul>';

            /** @var \Includes\DataStructure\Graph $child */
            foreach ($children as $child) {
                $data = $child->getData();

                $jstreeOptions = [];

                if ($data->isList) {
                    $jstreeOptions['disabled'] = true;
                }

                $additionalAttrs = [
                    'data-template-id'      => $data->templateId,
                    'data-template-path'    => $child->getKey(),
                    'data-template-weight'  => $data->viewListWeight,
                    'data-template-list'    => $data->viewList,
                    'data-user-generated'   => static::isUserGeneratedTemplate($child) ? 'true' : 'false',
                    'data-added-via-editor' => static::isAddedViaEditor($child) ? 'true' : 'false',
                ];

                $label = $data->class
                    ? sprintf('%s (%s)', $child->getKey(), $data->class)
                    : $child->getKey();

                $result .= sprintf(
                    '<li id="template_%s" data-jstree=\'%s\' %s><span class="template-weight">%s</span>%s%s</li>',
                    $data->templateId,
                    json_encode($jstreeOptions),
                    static::convertToHtmlAttributeString($additionalAttrs),
                    $data->viewListWeight,
                    $label,
                    static::buildHtmlTreeNode($child)
                );
            }

            $result .= '</ul>';
        }

        return $result;
    }

    /**
     * Checks if this file is a user-generated template
     *
     * @param \Includes\DataStructure\Graph $node
     *
     * @return bool
     */
    public static function isUserGeneratedTemplate($node)
    {
        $layout      = \XLite\Core\Layout::getInstance();
        $tweakerPath = $layout->getTweakerPathByLocalPath($node->getKey());

        return $layout->hasTweakerTemplate($tweakerPath);
    }

    /**
     * Checks if this file is a user-generated template
     *
     * @param \Includes\DataStructure\Graph $node
     *
     * @return bool
     */
    public static function isAddedViaEditor($node)
    {
        return static::hasNewTag($node->getKey());
    }

    /**
     * @param string $template
     *
     * @return bool
     */
    public static function hasNewTag(string $template)
    {
        $basename = basename($template);
        $suffix   = '.new.twig';

        // checking that basename ends with '.new.twig'
        return ($temp = strlen($basename) - strlen($suffix)) >= 0 && strpos($basename, $suffix, $temp) !== false;
    }

    /**
     * Returns current templates tree (HTML)
     *
     * @return string
     */
    public static function getJsonTree()
    {
        $result = static::buildJsonTreeNode(static::$tree);

        return json_encode($result);
    }

    /**
     * Returns current templates tree (JSON)
     *
     * @param \Includes\DataStructure\Graph $node Node
     *
     * @return array
     */
    public static function buildJsonTreeNode(\Includes\DataStructure\Graph $node)
    {
        $result = [];

        $children = $node->getChildren();

        if ($children) {
            /** @var \Includes\DataStructure\Graph $child */
            foreach ($children as $child) {
                $data = $child->getData();

                $label = $data->class
                    ? sprintf('%s (%s)', $child->getKey(), $data->class)
                    : $child->getKey();

                $result[] = [
                    'id'       => sprintf('template_%s', $data->templateId),
                    'text'     => $label,
                    'state'    => [
                        'disabled' => $data->isList,
                    ],
                    'li_attr'  => [
                        'data-template-id'   => $data->templateId,
                        'data-template-path' => $child->getKey(),
                    ],
                    'children' => static::buildJsonTreeNode($child),
                ];
            }
        }

        return $result;
    }

    protected function isCustomJsEnabled()
    {
        return ThemeTweaker::castCheckboxValue(\XLite\Core\Config::getInstance()->XC->ThemeTweaker->use_custom_js);
    }

    protected function isCustomCssEnabled()
    {
        return ThemeTweaker::castCheckboxValue(\XLite\Core\Config::getInstance()->XC->ThemeTweaker->use_custom_css);
    }

    /**
     * Return custom.css path
     * @return string
     */
    protected function getCustomCssPath()
    {
        return \XC\ThemeTweaker\Main::getThemeDir() . 'custom.css';
    }

    /**
     * Returns custom css text
     * @return string
     */
    protected function getCustomCssText()
    {
        return \Includes\Utils\FileManager::read($this->getCustomCssPath()) ?: '';
    }

    protected function getNextTemplateId()
    {
        return \XLite::getController()->isAJAX()
            ? substr(\XLite\Core\Request::getInstance()->getUniqueIdentifier(), 0, 6) . '_' . static::$templateId++
            : static::$templateId++;
    }

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

        [$templateWrapperText, $templateWrapperStart] = $this->startMarker($template);
        if ($templateWrapperText) {
            echo $templateWrapperStart;
            $result['templateWrapperText'] = $templateWrapperText;
        }

        return $result;
    }

    public function startMarker($template)
    {
        if ($this->setStartMark($template)) {
            $templateId = $this->getNextTemplateId();

            if (
                is_string($template)
                && static::hasNewTag($template)
            ) {
                $localPath = 'web/customer/' . $template;
            } else {
                $localPath = substr($template, strlen(LC_DIR_ROOT));
                $localPath = preg_replace('#(modules/(\w+)/(\w+)/)?templates/#', '', $localPath);
            }

            $current = new \XLite\Core\CommonGraph($localPath);

            $data             = new \XLite\Core\CommonCell();
            $data->class      = get_class($this);
            $data->templateId = $templateId;

            if ($this->viewListWeight) {
                $data->viewListWeight = $this->viewListWeight;
            }
            if ($this->viewListWeight) {
                $data->viewList = $this->viewList;
            }

            $current->setData($data);

            static::$current->addChild($current);
            static::$current = $current;

            $templateWrapperText = get_class($this) . ' : ' . $localPath . ' (' . $templateId . ')'
                . ($this->viewListName ? ' [\'' . $this->viewListName . '\' list child]' : '');

            return [$templateWrapperText, '<!-- ' . $templateWrapperText . ' {' . '{{ -->'];
        }

        return ['', ''];
    }

    /**
     * Finalize template display
     *
     * @param string $template     Template short path
     * @param array  $profilerData Profiler data which is calculated and returned in the 'prepareTemplateDisplay' method
     *
     * @return void
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        if (isset($profilerData['templateWrapperText'])) {
            echo $this->endMarker($template, $profilerData['templateWrapperText']);
        }

        parent::finalizeTemplateDisplay($template, $profilerData);
    }

    public function endMarker($template, $templateWrapperText)
    {
        static::$current = static::$current->getParent();
        $this->setEndMark($template);

        return '<!-- }}' . '} ' . $templateWrapperText . ' -->';
    }

    /**
     * Display view list content
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return void
     */
    public function displayViewListContent($list, array $arguments = [])
    {
        $start = false;
        if (static::$mark) {
            $templateId = $this->getNextTemplateId();

            $current = new \XLite\Core\CommonGraph($list);

            $data             = new \XLite\Core\CommonCell();
            $data->templateId = $templateId;
            $data->isList     = true;
            $current->setData($data);

            static::$current->addChild($current);
            static::$current = $current;
            $start           = true;
        }

        parent::displayViewListContent($list, $arguments);

        if ($start) {
            static::$current = static::$current->getParent();
        }
    }

    /**
     * Returns view list item widget params, used in getWidget call
     *
     * @param \XLite\Model\ViewList $item
     *
     * @return array
     */
    protected function getViewListItemWidgetParams(\XLite\Model\ViewList $item)
    {
        $params = parent::getViewListItemWidgetParams($item);

        if (ThemeTweaker::getInstance()->isInWebmasterMode()) {
            $params['viewListWeight'] = $item->getWeightActual();
            $params['viewList']       = $item->getListActual();
        }

        return $params;
    }

    protected function setStartMark($template)
    {
        if (static::$mark === null) {
            if (\XLite::isAdminZone()) {
                $zone = \XLite\Core\Request::getInstance()->zone ?: \XLite::ZONE_ADMIN;
                if ($this->checkNotificationRootTemplate($template, $zone)) {
                    static::$mark = $this->isMarkTemplates();
                }
            } else {
                static::$mark = $this->isMarkTemplates();
            }
        }

        return static::$mark;
    }

    protected function setEndMark($template)
    {
        if (static::$mark !== null) {
            if (\XLite::isAdminZone()) {
                $zone = \XLite\Core\Request::getInstance()->zone ?: \XLite::ZONE_ADMIN;
                if ($this->checkNotificationRootTemplate($template, $zone)) {
                    static::$mark = false;
                }
            }
        }
    }

    /**
     * @param string $template
     * @param string $zone
     *
     * @return boolean
     */
    protected function checkNotificationRootTemplate($template, $zone = \XLite::ZONE_ADMIN)
    {
        if (\XLite::getController()->getTarget() === 'notification_editor') {
            $templatesDirectory = \XLite\Core\Request::getInstance()->templatesDirectory;

            return $this->getNotificationRootTemplate($templatesDirectory, $zone) === $template;
        }

        return false;
    }

    /**
     * @param string $templateDirectory
     * @param string $zone
     *
     * @return bool
     */
    protected function getNotificationRootTemplate($templateDirectory, $zone = \XLite::ZONE_ADMIN)
    {
        return $this->executeCachedRuntime(
            function () use ($templateDirectory, $zone) {
                if ($this->notificationRootTemplate === null) {
                    $layout = \XLite\Core\Layout::getInstance();

                    $this->notificationRootTemplate = $layout->callInInterfaceZone(static function () use ($layout, $templateDirectory) {
                        return $layout->getResourceFullPath($templateDirectory . '/body.twig');
                    }, \XLite::INTERFACE_MAIL, $zone);
                }

                return $this->notificationRootTemplate;
            },
            [__CLASS__, __FUNCTION__, $templateDirectory, $zone]
        );
    }

    /**
     * Is running layout edit mode
     *
     * @return boolean
     */
    protected function isInLayoutMode()
    {
        return ThemeTweaker::getInstance()->isInLayoutMode();
    }

    /**
     * Is running custom css edit mode
     *
     * @return boolean
     */
    protected function isInCustomCssMode()
    {
        return ThemeTweaker::getInstance()->isInCustomCssMode();
    }

    /**
     * Display plain array as JS array
     *
     * @param array $data Plain array
     *
     * @return void
     */
    public function displayCommentedData(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof Markup) {
                $data[$key] = (string) $value;
            }
        }

        parent::displayCommentedData($data);
    }

    /**
     * Mark templates
     *
     * @return boolean
     */
    protected function isMarkTemplates()
    {
        if (static::$allowMark === null) {
            static::$allowMark = ThemeTweaker::getInstance()->isInWebmasterMode()
                || ThemeTweaker::getInstance()->isInEmailTemplateMode();
        }

        return static::$allowMark;
    }

    /**
     * Cache allowed
     *
     * @return boolean
     */
    protected function isCacheAllowed()
    {
        return parent::isCacheAllowed()
            && !ThemeTweaker::getInstance()->isInWebmasterMode()
            && !\XLite\Core\Translation::getInstance()->isInlineEditingEnabled();
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $url = parent::getAppleIcon();

        return ThemeTweaker::getInstance()->isInLayoutMode()
            ? $url . '?' . time()
            : $url;
    }

    /**
     * Return favicon resource path
     *
     * @return string
     */
    protected function getFavicon()
    {
        $url = parent::getFavicon();

        return ThemeTweaker::getInstance()->isInLayoutMode()
            ? $url . '?' . time()
            : $url;
    }
}

// Call static constructor
\XC\ThemeTweaker\View\AView::__constructStatic();
