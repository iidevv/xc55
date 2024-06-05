<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Abstract handler (common parent for viewer and controller)
 */
abstract class Handler extends \XLite\Base
{
    /**
     * Controller-specific params
     */
    public const PARAM_SILENT       = 'silent';

    /**
     * AJAX-specific parameters
     */
    public const PARAM_AJAX_TARGET = 'target';
    public const PARAM_AJAX_WIDGET = 'widget';

    /**
     * Widget params
     *
     * @var array
     */
    protected $widgetParams;


    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct();

        $this->setWidgetParams($params);
    }

    /**
     * Initialize handler
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        $listParams = $this->getWidgetParams();

        foreach ($listParams as $name => $paramObject) {
            if (0 < strlen($name) && isset($params[$name])) {
                $paramObject->setValue($params[$name]);
            }
            // FIXME - for mapping only
            // FIXME - uncomment (at first), remove after check
            // unset($params[$name]);
        }

        // FIXME - backward compatibility - mapping; to remove
        foreach ($params as $name => $value) {
            if (
                0 < strlen($name)
                && !in_array(strtolower($name), ['iscloned', 'requestparams'], true)
            ) {
                $this->$name = $value;
            }
        }
    }

    /**
     * Return widget parameters list (or a single object)
     *
     * @param string $param Param name OPTIONAL
     *
     * @return \XLite\Model\WidgetParam\AWidgetParam[]|\XLite\Model\WidgetParam\AWidgetParam
     */
    public function getWidgetParams($param = null)
    {
        if ($this->widgetParams === null) {
            $this->defineWidgetParams();
        }

        return $param !== null
            ? ($this->widgetParams[$param] ?? null)
            : $this->widgetParams;
    }

    /**
     * @return array
     */
    public function getWidgetSettings()
    {
        return array_filter(
            $this->getWidgetParams(),
            [$this, 'getWidgetSettingsFilter']
        );
    }

    /**
     * Filter for getWidgetSettings() method
     *
     * @param \XLite\Model\WidgetParam\AWidgetParam $param Widget parameter
     *
     * @return boolean
     */
    public function getWidgetSettingsFilter(\XLite\Model\WidgetParam\AWidgetParam $param)
    {
        return $param->isSetting;
    }

    /**
     * Check passed attributes
     *
     * @param array $attrs Attributes to check
     *
     * @return array Errors list
     */
    public function validateAttributes(array $attrs)
    {
        $messages = [];

        foreach ($this->getWidgetSettings() as $name => $param) {
            if (isset($attrs[$name])) {
                [$result, $widgetErrors] = $param->validate($attrs[$name]);

                if ($result === false) {
                    $messages[] = $param->label . ': ' . implode('<br />' . $param->label . ': ', $widgetErrors);
                }
            } else {
                $messages[] = $param->label . ': is not set';
            }
        }

        return $messages;
    }

    /**
     * Compose URL from target, action and additional params
     *
     * @param string  $target      Page identifier OPTIONAL
     * @param string  $action      Action to perform OPTIONAL
     * @param array   $params      Additional params OPTIONAL
     * @param boolean $forceCuFlag Force flag - use Clean URL OPTIONAL
     *
     * @return string
     */
    public function buildURL($target = '', $action = '', array $params = [], $forceCuFlag = null)
    {
        if ($target !== '' && ($action !== '' || (isset($params['action']) && $params['action'] !== ''))) {
            $class = \XLite\Core\Converter::getControllerClass($target);
            $paramAction = $params['action'] ?? $action;

            if (
                $class
                && $class::needFormId()
                && $paramAction
                && !in_array($paramAction, $class::defineFreeFormIdActions(), true)
            ) {
                $params[\XLite::FORM_ID] = \XLite::getFormId(empty($params['static_form_id']));
            }
        }

        return \XLite\Core\Converter::buildURL($target, $action, $params, null, false, $forceCuFlag);
    }

    /**
     * Compose URL from target, action and additional params
     *
     * @param \XLite\Model\AccessControlCell $acc
     * @param string                         $resendMethod resend method name in \XLite\Controller\Customer\AccessControl
     * @param string  $target      Page identifier
     * @param string  $action      Action to perform
     * @param array   $params      Additional params
     *
     * @return string
     */
    public function buildPersistentAccessURL(\XLite\Model\AccessControlCell $acc, $target = '', $action = '', array $params = [])
    {
        return \XLite\Core\Converter::buildPersistentAccessURL($acc, $target, $action, $params);
    }

    /**
     * Compose complete URL from target, action and additional params
     *
     * @param string $target Page identifier OPTIONAL
     * @param string $action Action to perform OPTIONAL
     * @param array  $params Additional params OPTIONAL
     *
     * @return string
     */
    public function buildFullURL($target = '', $action = '', array $params = [])
    {
        return \XLite\Core\Converter::buildFullURL($target, $action, $params);
    }

    /**
     * Compose URL path from target, action and additional params
     * FIXME - this method must be removed
     *
     * @param string $target Page identifier
     * @param string $action Action to perform OPTIONAL
     * @param array  $params Additional params OPTIONAL
     *
     * @return string
     */
    public function buildURLPath($target, $action = '', array $params = [])
    {
        $url = $this->buildURL($target, $action, $params);
        $parts = parse_url($url);

        return (!isset($parts['path']) || strlen($parts['path'])) ? './' : $parts['path'];
    }

    /**
     * Compose URL query arguments from target, action and additional params
     * FIXME - this method must be removed
     *
     * @param string $target Page identifier
     * @param string $action Action to perform OPTIONAL
     * @param array  $params Additional params OPTIONAL
     *
     * @return array
     */
    public function buildURLArguments($target, $action = '', array $params = [])
    {
        $url = $this->buildURL($target, $action, $params);
        $parts = parse_url($url);

        $args = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $args);
        }

        return $args;
    }

    /**
     * Common prefix for editable elements in lists
     *
     * NOTE: this method is requered for the GetWidget and AAdmin classes
     * TODO: after the multiple inheritance should be moved to the AAdmin class
     *
     * @return string
     */
    public function getPrefixPostedData()
    {
        return 'postedData';
    }

    /**
     * Common prefix for the selected checkboxes in lists
     *
     * NOTE: this method is requered for the GetWidget and AAdmin classes
     * TODO: after the multiple inheritance should be moved to the AAdmin class
     *
     * @return string
     */
    public function getPrefixSelected()
    {
        return 'select';
    }

    // {{{ Methods to work with the received data

    /**
     * @param string $prefix Index in the request array
     *
     * @return array
     */
    protected function getRequestDataByPrefixArray($prefix)
    {
        return (array) \XLite\Core\Request::getInstance()->$prefix;
    }

    /**
     * @param string $prefix Index in the request array
     * @param string $field  Name of the field to retrieve OPTIONAL
     *
     * @return array|mixed
     */
    protected function getRequestDataByPrefix($prefix, $field = null)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getRequestDataByPrefixArray($prefix), $field);
    }

    /**
     * @param string $field Name of the field to retrieve OPTIONAL
     *
     * @return array|mixed
     */
    protected function getPostedData($field = null)
    {
        return $this->getRequestDataByPrefix($this->getPrefixPostedData(), $field);
    }

    /**
     * Return selected index array
     *
     * @return array
     */
    protected function getSelected()
    {
        return $this->getRequestDataByPrefix($this->getPrefixSelected());
    }

    // }}}

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        $this->widgetParams = [];
    }

    /**
     * Return widget param value
     *
     * @param string $param Param to fetch
     *
     * @return mixed
     */
    protected function getParam($param)
    {
        $param = $this->getWidgetParams($param);

        return $param ? $param->value : $param;
    }

    /**
     * @param array $params List of params to use
     *
     * @return array
     */
    protected function getParamsHash(array $params)
    {
        $result = [];

        foreach ($params as $param) {
            $result[$param] = $this->getParam($param);
        }

        return $result;
    }
}
