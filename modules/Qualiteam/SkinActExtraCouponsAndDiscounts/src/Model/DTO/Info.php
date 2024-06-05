<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Model\DTO;

use CDev\Coupons\Model\Coupon;
use Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\Model\DTO\Base\CommonCell;

class Info extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\DTO\Info $dto
     * @param ExecutionContextInterface                                 $context
     */
    public static function validate($dto, ExecutionContextInterface $context)
    {
        if (!static::isCouponCodeValid($dto)) {
            static::addViolation($context, 'default.coupon_code', Translation::lbl('SkinActExtraCouponsAndDiscounts this coupon code already exists'));
        }
    }

    protected static function isCouponCodeValid(Info $dto)
    {
        $code = $dto->default->coupon_code;
        $id = $dto->default->identity;

        $proMembershipCouponCount = Database::getRepo(ExtraCouponsAndDiscounts::class)
            ->findDuplicatesCount($code, $id);

        $couponCount = Database::getRepo(Coupon::class)
            ->findDuplicatesProMembershipCouponsCount($code);

        return $proMembershipCouponCount === 0 && $couponCount === 0;
    }

    /**
     * @param mixed|ExtraCouponsAndDiscounts $object
     */
    protected function init($object)
    {
        $default       = [
            'identity'           => $object->getId(),
            'title'              => $object->getTitle(),
            'stamp_text_1'       => $object->getStampText1(),
            'stamp_text_2'       => $object->getStampText2(),
            'coupon_code'        => $object->getCouponCode(),
            'type'               => $object->getType(),
            'value'              => $object->getValue(),
            'description'        => $object->getDescription(),
            'additional_content' => $object->getAdditionalContent(),
        ];
        $this->default = new CommonCell($default);
    }

    /**
     * @param ExtraCouponsAndDiscounts $object
     * @param array|null               $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $description = $this->isContentTrustedByPermission('description')
            ? (string) $rawData['default']['description']
            : \XLite\Core\HTMLPurifier::purify((string) $rawData['default']['description']);

        $additionalContent = $this->isContentTrustedByPermission('additional_content')
            ? (string) $rawData['default']['additional_content']
            : \XLite\Core\HTMLPurifier::purify((string) $rawData['default']['additional_content']);

        $object->setTitle($default->title);
        $object->setStampText1($default->stamp_text_1);
        $object->setStampText2($default->stamp_text_2);
        $object->setCouponCode($default->coupon_code);
        $object->setType($default->type);
        $object->setValue($default->value);
        $object->setDescription($description);
        $object->setAdditionalContent($additionalContent);
    }

    /**
     * @param ExtraCouponsAndDiscounts $object
     * @param array|null               $rawData
     */
    public function afterUpdate($object, $rawData = null)
    {
        $coupon = $object->getCoupon();

        if ($coupon) {
            $coupon->setValue($object->getValue());
            $coupon->setType($object->getType());
            $coupon->setCode($object->getCouponCode());
            $coupon->update();
        }
    }

    /**
     * @param ExtraCouponsAndDiscounts $object
     * @param array|null               $rawData
     */
    public function afterCreate($object, $rawData = null)
    {
        $coupon = new \CDev\Coupons\Model\Coupon;
        $coupon->setCode($object->getCouponCode());
        $coupon->setType($object->getType());
        $coupon->setValue($object->getValue());
        $coupon->setComment('Pro membership coupon');
        $coupon->setExtraCoupon($object);
        $coupon->create();

        $object->setCoupon($coupon);
    }
}