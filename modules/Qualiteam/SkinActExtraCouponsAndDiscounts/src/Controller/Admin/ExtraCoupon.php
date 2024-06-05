<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Admin;

use Qualiteam\SkinActExtraCouponsAndDiscounts\Model\DTO\Info;
use XLite\Core\Database;
use XLite\Core\TopMessage;

class ExtraCoupon extends \XLite\Controller\Admin\ACL\Catalog
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    /**
     * Backward compatibility
     *
     * @var array
     */
    protected $params = ['target', 'id', 'page', 'backURL'];

    /**
     * Chuck length
     */
    public const CHUNK_LENGTH = 100;

    // {{{ Abstract method implementations

    /**
     * Check if we need to create new video or modify an existing one
     *
     * NOTE: this function is public since it's neede for widgets
     *
     * @return bool
     */
    public function isNew()
    {
        return !$this->getExtraCoupon()->isPersistent();
    }

    /**
     * Alias
     *
     * @return \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts
     */
    protected function getEntity()
    {
        return $this->getExtraCoupon();
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            static::t('SkinActExtraCouponsAndDiscounts extra coupons and discounts'),
            $this->buildURL('extra_coupons_and_discounts')
        );
    }

    // }}}

    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list['info'] = static::t('SkinActExtraCouponsAndDiscounts info');

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['info']    = 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/extra_coupon.twig';
        $list['default'] = 'modules/Qualiteam/SkinActExtraCouponsAndDiscounts/extra_coupon.twig';

        return $list;
    }

    // }}}

    // {{{ Data management

    /**
     * Alias
     *
     * @return \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts
     */
    public function getExtraCoupon()
    {
        $result = $this->extraCouponCache
            ?: Database::getRepo(\Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts::class)->find($this->getExtraCouponId());

        if ($result === null) {
            $result = new \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts;
        }

        return $result;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getExtraCoupon() && $this->getExtraCoupon()->isPersistent()
            ? $this->getExtraCoupon()->getTitle()
            : static::t('SkinActExtraCouponsAndDiscounts new coupon');
    }

    /**
     * Return current video Id
     *
     * NOTE: this function is public since it's neede for widgets
     *
     * @return integer
     */
    public function getExtraCouponId()
    {
        $result = $this->extraCouponCache
            ? $this->extraCouponCache->getExtraCouponId()
            : (int) \XLite\Core\Request::getInstance()->id;

        if (0 >= $result) {
            $result = (int) \XLite\Core\Request::getInstance()->id;
        }

        return $result;
    }

    /**
     * The video can be set from the view classes
     *
     * @param \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts $extraCoupon Video
     */
    public function setVideo(\Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts $extraCoupon)
    {
        $this->extraCouponCache = $extraCoupon;
    }

    // }}}

    // {{{ Action handlers

    protected function doActionUpdate()
    {
        $dto = $this->getFormModelObject();
        $extraCoupon = $this->getExtraCoupon();
        $isPersistent = $extraCoupon->isPersistent();

        $formModel = new \Qualiteam\SkinActExtraCouponsAndDiscounts\View\FormModel\ExtraCoupon(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($extraCoupon, $rawData[$this->formName]);

            if ($extraCoupon->getId()) {
                Database::getEM()->getUnitOfWork()->scheduleForUpdate($extraCoupon);
            }
            Database::getEM()->persist($extraCoupon);
            Database::getEM()->flush();

            if (!$isPersistent) {
                $dto->afterCreate($extraCoupon, $rawData[$this->formName]);
                TopMessage::addInfo('SkinActExtraCouponsAndDiscounts coupon has been created');
            } else {
                $dto->afterUpdate($extraCoupon, $rawData[$this->formName]);
                TopMessage::addInfo('SkinActExtraCouponsAndDiscounts coupon has been updated');
            }
            Database::getEM()->flush();
        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);

            foreach ($form->getErrors(true) as $error) {
                TopMessage::addError($error->getMessage());
            }
        }

        $extraCouponId = $extraCoupon->getId() ?: $this->getExtraCouponId();

        $params = $extraCouponId ? ['id' => $extraCouponId] : [];

        $this->setReturnURL($this->buildURL('extra_coupon', '', $params));
    }

    /**
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        return new Info($this->getExtraCoupon());
    }

    /**
     * Purify an attribute value
     *
     * @param array $value
     *
     * @return array
     */
    protected function purifyValue($value)
    {
        $value['value'] = \XLite\Core\HTMLPurifier::purify($value['value']);

        return $value;
    }

    // }}}
}