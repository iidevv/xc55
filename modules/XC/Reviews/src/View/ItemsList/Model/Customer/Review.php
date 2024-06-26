<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\ItemsList\Model\Customer;

/**
 * Review details
 *
 */
class Review extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Widget param names
     */
    public const PARAM_PRODUCT_ID = 'product_id';
    public const PARAM_CATEGORY_ID = 'category_id';
    public const PARAM_SEARCH_ADDITION_DATE = 'additionDate';
    public const PARAM_SEARCH_STATUS = 'status';
    public const PARAM_SEARCH_REVIEWER_NAME = 'reviewerName';
    public const PARAM_SEARCH_EMAIL = 'email';
    public const PARAM_SEARCH_REVIEW = 'review';
    public const PARAM_SEARCH_KEYWORDS = 'keywords';
    public const PARAM_SEARCH_RATING = 'rating';
    public const PARAM_SEARCH_PRODUCT = 'product';

    public const WIDGET_TARGET = 'product_reviews';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = static::getWidgetTarget();

        return $result;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XC\Reviews\Model\Repo\Review::SEARCH_ADDITION_DATE => static::PARAM_SEARCH_ADDITION_DATE,
            \XC\Reviews\Model\Repo\Review::SEARCH_STATUS => static::PARAM_SEARCH_STATUS,
            \XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT => static::PARAM_SEARCH_PRODUCT,
            \XC\Reviews\Model\Repo\Review::SEARCH_KEYWORDS => static::PARAM_SEARCH_KEYWORDS,
            \XC\Reviews\Model\Repo\Review::SEARCH_RATING => static::PARAM_SEARCH_RATING,
        ];
    }

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/reviews_page/style.css';
        $list[] = 'modules/XC/Reviews/vote_bar/vote_bar.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = self::getDir() . LC_DS . self::getPageBodyDIr() . LC_DS . 'reviews_list.js';

        return $list;
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' product-reviews';
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);
    }

    // {{{ Search

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = self::PARAM_PRODUCT_ID;
        $this->requestParams[] = self::PARAM_CATEGORY_ID;
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
            $paramValue = $this->getParam($requestParam);

            if ($requestParam == static::PARAM_SEARCH_ADDITION_DATE && is_array($paramValue)) {
                foreach ($paramValue as $i => $date) {
                    if (is_string($date) && strtotime($date) !== false) {
                        $paramValue[$i] = strtotime($date);
                    }
                }
            }

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\XC\Reviews\Model\Repo\Review::P_ORDER_BY} = ['r.additionDate', 'DESC'];

        $profile = $this->getReviewerProfile();
        $result->{\XC\Reviews\Model\Repo\Review::SEARCH_ZONE}
            = [\XC\Reviews\Model\Repo\Review::SEARCH_ZONE_CUSTOMER, $profile];

        $result->{\XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT} = $this->getProduct();
        $result->{\XC\Reviews\Model\Repo\Review::SEARCH_TYPE}
            = \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY;

        return $result;
    }

    // }}}

    /**
     * Return true if review is approved
     *
     * @return boolean
     */
    protected function isApproved(\XC\Reviews\Model\Review $entity)
    {
        return $entity->getStatus() == \XC\Reviews\Model\Review::STATUS_APPROVED;
    }

    /**
     * Return true if review is in pending status
     *
     * @return boolean
     */
    protected function isOnModeration(\XC\Reviews\Model\Review $entity)
    {
        return (
            !$this->isApproved($entity)
            && (\XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews == true)
        );
    }

    /**
     * Return reviews list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|void
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XC\Reviews\Model\Review')->search($cnd, $countOnly);
    }

    /**
     * Return current product's category id
     *
     * @return integer
     */
    protected function getCategoryId()
    {
        $categoryId = null;

        if (\XLite::getController()->getCategoryId()) {
            $categoryId = \XLite::getController()->getCategoryId();
        } elseif ($this->getProduct()) {
            $categoryId = $this->getProduct()->getCategoryId();
        }

        return $categoryId;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
    }

    /**
     * Define widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Reviews';
    }

    /**
     * Define page body templates directory
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'reviews_page';
    }

    /**
     * Define page body template
     *
     * @return string
     */
    protected function getPageBodyFile()
    {
         return 'reviews.twig';
    }

    /**
     * Get CSS class
     *
     * @return string
     */
    protected function getClass(\XC\Reviews\Model\Review $entity)
    {
        return (
            $this->isApproved($entity)
            || (\XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews == false)
        )
            ? ''
            : ' pending';
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return 0 < $this->getItemsCount();
    }

    /**
     * Get empty list template name
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getDir() . LC_DS . $this->getPageBodyDir() . '/empty_reviews_list.twig';
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();
        $list[self::PARAM_CATEGORY_ID] = $this->getCategoryId();
        $list[self::PARAM_PRODUCT_ID] = $this->getProductId();

        return $list;
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
            self::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Category ID',
                $this->getCategoryId()
            ),
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\ObjectId\Product(
                'Product ID',
                $this->getProductId()
            ),
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Get JS handler class name (used for pagination)
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'ReviewsList';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XC\Reviews\Model\Review';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('review');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' reviews';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XC\Reviews\View\Pager\Customer\Review';
    }
}
