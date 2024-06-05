<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\ItemsList\Model;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;

/**
 * Products item list
 */
class AddedPreviouslyProduct extends \XLite\View\ItemsList\Model\ProductSelection
{
    use ExecuteCachedTrait;

    public const PARAM_ORIG_PROFILE_ID = 'origProfileId';

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->sortByModes += [
            static::SORT_BY_AMOUNT => 'Amount'
        ];
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['added_previously_product']);
    }

    /**
     * Return wrapper form options
     *
     * @return array
     */
    protected function getFormOptions()
    {
        $options = parent::getFormOptions();

        $options['class'] = '\Qualiteam\SkinActCreateOrder\View\Form\AddedPreviouslyProduct\Table';
        $options['target'] = null;
        $options['action'] = null;
        $options['params'] = null;

        return $options;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'added_previously_product';
    }

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
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
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return false;
    }

    protected function getSelectorActionTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/added_previously_product/parts/select.twig';
    }

    protected function getListNameSuffixes()
    {
        return ['added_previously_product'];
    }

    public function getSelectedProfile()
    {
        $selectedProfile = null;

        $order = $this->getOrder();
        if ($order) {
            $profileId = \XLite\Core\Request::getInstance()->profileId;
            $selectedProfile = $order->getOrigProfile() ?? $this->getProfileById($profileId);
        }

        return $selectedProfile;
    }

    protected function getProfileById($profileId)
    {
        return $this->executeCachedRuntime(function () use ($profileId) {
            return Database::getRepo('XLite\Model\Profile')
                ->findOneBy(['profile_id' => $profileId]);
        }, [
            self::class,
            __METHOD__,
            $profileId
        ]);
    }

    protected function isGetSelectedProfile()
    {
        return (bool) $this->getSelectedProfile();
    }

    protected function getSelectedProfileId()
    {
        $profile = $this->getSelectedProfile();

        if ($profile) {
            return $profile->getProfileId();
        }

        return 0;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\Qualiteam\SkinActCreateOrder\Model\Repo\Product::SEARCH_ORIG_PROFILE_ID} = $this->isGetSelectedProfile()
            ? $this->getSelectedProfileId()
            : \XLite\Core\Request::getInstance()->{static::PARAM_ORIG_PROFILE_ID};

        return $result;
    }

    /**
     * Define columns structure
     *
     * @return array
     */

    protected function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get main column
     *
     * @return array
     */
    protected function getMainColumn()
    {
        return null;
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\StickyPanel\ItemsList\AddedPreviouslyProduct';
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\SearchPanel\AddedPreviouslyProduct\Admin\Main';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\Pager\Admin\Model\Table';
    }

    protected function getCommonParams()
    {
        $commonParams = parent::getCommonParams();

        if ($this->getOrder()) {
            $commonParams['order_number'] = $this->getOrder()->getOrderNumber();
        }

        if ($this->isGetSelectedProfile()) {
            $commonParams['origProfileId'] = $this->getSelectedProfileId();
        }

        if ($this->getExceptedProducts()) {
            $commonParams['productIds'] = $this->getExceptedProducts();
        }

        $commonParams['getter'] = $this->buildURL('model_order_item_selector', '', [
            'order_id' => $this->getOrder()->getOrderId()
        ]);

        $this->commonParams = $commonParams;

        return $this->commonParams;
    }

    protected function getExceptedProducts()
    {
        return \XLite\Core\Request::getInstance()->productIds ?? 0;
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/add_previously_product_controller.js';

        return $list;
    }

    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' added_previously_product';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORIG_PROFILE_ID => new \XLite\Model\WidgetParam\TypeInt(
                'OrigProfileID ',
                $this->isGetSelectedProfile() ? $this->getSelectedProfileId() : null
            ),
        ];
    }

    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if ($this->isGetSelectedProfile()) {
            $this->widgetParams[static::PARAM_ORIG_PROFILE_ID]->setValue($this->getSelectedProfileId());
        } else {
            $this->widgetParams[static::PARAM_ORIG_PROFILE_ID]->setValue(0);
        }
    }

    public static function getSearchParams()
    {
        return array_merge([
            static::PARAM_ORIG_PROFILE_ID => static::PARAM_ORIG_PROFILE_ID,
        ], parent::getSearchParams());
    }

    protected function postprocessSearchCase(\XLite\Core\CommonCell $cnd)
    {
        $cnd = parent::postprocessSearchCase($cnd);
        $cnd->{static::PARAM_ORIG_PROFILE_ID} = $this->getSelectedProfileId();

        return $cnd;
    }
}
