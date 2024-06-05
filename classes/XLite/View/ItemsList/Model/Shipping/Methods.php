<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

/**
 * Shipping methods list
 */
class Methods extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    public const PARAM_PROCESSOR = 'processor';

    public const SORT_BY_MODE_NAME = 'translations.name';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            self::SORT_BY_MODE_NAME => 'Name',
        ];

        parent::__construct($params);
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'shipping_methods';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'processor' => \XLite::getController()->getProcessorId(),
            ]
        );
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/shipping/methods/style.css';

        return $list;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Shipping\Method';
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();
        $list['processor'] = $this->getProcessorId();

        return $list;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        parent::getCommonParams();

        $this->commonParams['processor'] = $this->getProcessorId();

        return $this->commonParams;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'name' => [
                static::COLUMN_NAME  => static::t('Carrier service'),
                static::COLUMN_CLASS => !$this->getProcessor() || $this->getProcessor()->isMethodNamesAdjustable()
                    ? 'XLite\View\FormField\Inline\Input\Text'
                    : null,
                static::COLUMN_SORT     => static::SORT_BY_MODE_NAME,
                static::COLUMN_ORDERBY  => 100,
            ],
        ];

        if (0 < \XLite\Core\Database::getRepo('XLite\Model\TaxClass')->count()) {
            $columns['taxClass'] = [
                static::COLUMN_NAME   => static::t('Tax class'),
                static::COLUMN_CLASS  => 'XLite\View\FormField\Inline\Select\ShippingTaxClass',
                static::COLUMN_PARAMS => [
                    'fieldOnly' => true,
                ],
                static::COLUMN_ORDERBY  => 200,
            ];
        }

        if (!$this->getProcessor() || $this->getProcessor()->getProcessorId() === 'offline') {
            $columns['rates'] = [
                static::COLUMN_NAME   => static::t('Rates'),
                static::COLUMN_TEMPLATE  => false,
                static::COLUMN_ORDERBY  => 300,
            ];
        }

        return $columns;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['shipping-methods'];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' shipping-methods';
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add shipping method';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return (!$this->getProcessor() || $this->getProcessor()->isMethodDeleteEnabled())
            ? static::CREATE_INLINE_TOP
            : static::CREATE_INLINE_NONE;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return (!$this->getProcessor() || $this->getProcessor()->isMethodDeleteEnabled())
            ? static::CREATE_INLINE_TOP
            : static::CREATE_INLINE_NONE;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return !$this->getProcessor() || $this->getProcessor()->isMethodDeleteEnabled();
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Get current processor id
     *
     * @return string
     */
    protected function getProcessorId()
    {
        return \XLite::getController()->getProcessorId();
    }

    /**
     * Return true if shipping methods' name can be edited
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        $result = null;

        $list = \XLite\Model\Shipping::getProcessors();

        $processorId = $this->getProcessorId() ?: 'offline';

        /** @var \XLite\Model\Shipping\Processor\AProcessor $processor */
        foreach ($list as $processor) {
            if ($processor->getProcessorId() === $processorId) {
                $result = $processor;
                break;
            }
        }

        return $result;
    }

    // {{{ Search

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Shipping\Method::P_CARRIER => static::PARAM_PROCESSOR,
        ];
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PROCESSOR => new \XLite\Model\WidgetParam\TypeString('Processor code', ''),
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

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->$modelParam = $this->getParam($requestParam);
        }

        $carrierParam = \XLite\Model\Repo\Shipping\Method::P_CARRIER;

        if (!empty($result->{$carrierParam}) && $result->{$carrierParam} === 'offline') {
            $result->{$carrierParam} = '';
        }

        return $result;
    }

    // }}}

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return \XLite\Core\Translation::lbl('X shipping methods have been created', ['count' => $count]);
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X shipping methods have been removed', ['count' => $count]);
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        // Set up processor
        $entity->setProcessor($this->getProcessorId() ?: 'offline');

        // Set up carrier if it's not 'offline'
        $entity->setCarrier(
            $this->getProcessorId() && $this->getProcessorId() !== 'offline'
                ? $this->getProcessorId()
                : ''
        );

        // Enable new method
        $entity->setEnabled(true);

        return $entity;
    }

    /**
     * Get pager parameters
     *
     * @return array
     */
    protected function getPagerParams()
    {
        $params = parent::getPagerParams();
        $params[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE] = 50;

        return $params;
    }

    /**
     * Get template name to display when list is empty
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/shipping/methods/empty.twig';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\ShippingMethods';
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_NAME;
    }
}
