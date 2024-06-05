<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\ItemsList\Model;

/**
 * Abstract records items list
 */
abstract class ARecord extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    public const PARAM_SEARCH_DATE_RANGE = 'dateRange';
    public const PARAM_SEARCH_STATE      = 'state';
    public const PARAM_SEARCH_SUBSTRING  = 'substring';
    public const PARAM_SEARCH_INCLUDING  = 'including';

    /**
     * Sort modes
     *
     * @var   array
     */
    protected $sortByModes = [
        'ptranslations.name' => 'Product name',
        'profile.login'      => 'Customer email',
        'r.date'             => 'Creation date',
        'r.state'            => 'State',
        'r.backDate'         => 'Back date',
        'r.sentDate'         => 'Send date',
        'r.price'            => 'Desired price',
        'r.quantity'         => 'Desired quantity',
    ];

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SEARCH_DATE_RANGE => new \XLite\Model\WidgetParam\TypeCollection('Date range', []),
            static::PARAM_SEARCH_STATE      => new \XLite\Model\WidgetParam\TypeInt('State', 0),
            static::PARAM_SEARCH_SUBSTRING  => new \XLite\Model\WidgetParam\TypeString('Substring', ''),
            static::PARAM_SEARCH_INCLUDING  => new \XLite\Model\WidgetParam\TypeCollection('Including', []),
        ];
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
     * Get search case (aggregated search conditions) processor
     * This should be passed in here by the controller, but i don't see appropriate way to do so
     *
     * @return \XLite\View\ItemsList\ISearchCaseProvider
     */
    public static function getSearchCaseProcessor()
    {
        return new \XLite\View\ItemsList\SearchCaseProcessor(
            static::getSearchParams(),
            static::getSearchValuesStorage()
        );
    }

    /**
     * Should search params values be saved to session or not
     *
     * @return boolean
     */
    protected function saveSearchConditions()
    {
        return true;
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy()
    {
        return [$this->getSortBy(), $this->getSortOrder()];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' records';
    }

    /**
     * @inheritdoc
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        return $column[static::COLUMN_CODE] === 'profile'
            ? $entity->getProfile()
            : parent::isLink($column, $entity);
    }

    /**
     * @inheritdoc
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        switch ($column[static::COLUMN_CODE]) {
            case 'product':
                $result = $this->buildURL(
                    $column[static::COLUMN_LINK],
                    '',
                    ['product_id' => $entity->getProduct()->getProductId()]
                );
                break;

            case 'profile':
                $result = $this->buildURL(
                    $column[static::COLUMN_LINK],
                    '',
                    ['profile_id' => $entity->getProfile()->getProfileId()]
                );
                break;

            default:
                $result = parent::buildEntityURL($entity, $column);
        }

        return $result;
    }

    // {{{ Behaviors

    /**
     * @inheritdoc
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    // {{{ Search

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        return array_merge(
            parent::getSearchParams(),
            [
                static::PARAM_SEARCH_SUBSTRING  => [
                    'condition' => new \XLite\Model\SearchCondition\RepositoryHandler(\QSL\BackInStock\Model\Repo\AbsRecord::SEARCH_SUBSTRING),
                    'widget'    => [
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                        \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER   => static::t('Search keywords'),
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY    => true,
                    ],
                ],
                static::PARAM_SEARCH_DATE_RANGE => [
                    'condition' => new \XLite\Model\SearchCondition\RepositoryHandler(\QSL\BackInStock\Model\Repo\AbsRecord::SEARCH_DATE_RANGE),
                    'widget'    => [
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY    => true,
                    ],
                ],
                static::PARAM_SEARCH_STATE      => [
                    'condition' => new \XLite\Model\SearchCondition\RepositoryHandler(\QSL\BackInStock\Model\Repo\AbsRecord::SEARCH_STATE),
                    'widget'    => [
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_CLASS                        => 'QSL\BackInStock\View\FormField\Select\State',
                        \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY                           => true,
                        \QSL\BackInStock\View\FormField\Select\State::PARAM_DISPLAY_ALL => true,
                    ],
                ],
                'by_conditions'                 => [
                    'widget' => [
                        \XLite\View\SearchPanel\SimpleSearchPanel::CONDITION_TYPE => \XLite\View\SearchPanel\SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        \XLite\View\SearchPanel\ASearchPanel::CONDITION_TEMPLATE  => 'modules/QSL/BackInStock/records/condition.by_conditions.twig',
                    ],
                ],
                static::PARAM_SEARCH_INCLUDING  => [
                    'condition' => new \XLite\Model\SearchCondition\RepositoryHandler(\QSL\BackInStock\Model\Repo\AbsRecord::SEARCH_INCLUDING),
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\QSL\BackInStock\Model\Repo\AbsRecord::SEARCH_ORDERBY} = $this->getOrderBy();

        return $result;
    }

    // }}}

    // {{{ Data getters and converters

    /**
     * Get product column value
     *
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return \XLite\Model\Product
     */
    protected function getProductColumnValue(\QSL\BackInStock\Model\ARecord $entity)
    {
        return $entity->getProduct();
    }

    /**
     * Get profile column value
     *
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function getProfileColumnValue(\QSL\BackInStock\Model\ARecord $entity)
    {
        return $entity->getEmail();
    }

    /**
     * Preprocess product value
     *
     * @param \XLite\Model\Product                       $value Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessProduct(\XLite\Model\Product $value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        return $value->getName();
    }

    /**
     * Preprocess state value
     *
     * @param integer                                    $value  Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessState($value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        switch ($value) {
            case \QSL\BackInStock\Model\ARecord::STATE_STANDBY:
                $result = static::t('Stand-by');
                break;

            case \QSL\BackInStock\Model\ARecord::STATE_BOUNCED:
                $result = static::t('Bounced');
                break;
            case \QSL\BackInStock\Model\ARecord::STATE_READY:
                $result = static::t('Ready for send');
                break;

            case \QSL\BackInStock\Model\ARecord::STATE_SENT:
                $result = static::t('Sent');
                break;

            case \QSL\BackInStock\Model\ARecord::STATE_SENDING:
                $result = static::t('Sending');
                break;

            default:
                $result = $value;
        }

        return $result;
    }

    /**
     * Preprocess date value
     *
     * @param integer                                    $value  Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessDate($value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        return $this->formatDate($value);
    }

    /**
     * Preprocess sent date value
     *
     * @param integer                                    $value  Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessSentDate($value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        return $value ? $this->formatDate($value) : static::t('n/a');
    }

    /**
     * Preprocess back date value
     *
     * @param integer                                    $value  Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessBackDate($value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        return $value ? $this->formatDate($value) : static::t('n/a');
    }

    // }}}
}
