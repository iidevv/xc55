<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Controller\Admin;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Mailer;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\Product;
use XLite\Model\Profile;

class BuyProMembership extends \XLite\Controller\Admin\AAdmin
{
    public function getTitle()
    {
        return static::t('SkinActProMembership select pro membership product');
    }

    public static function needFormId()
    {
        return false;
    }

    public function doActionSendMessage()
    {
        $profileId = Request::getInstance()->profile_id;
        $productId = Request::getInstance()->product_id ?? 0;
        $isOpeningPopup = Request::getInstance()->isOpeningPopup;

        if ($profileId && $productId) {
            $profile = $this->getDbProfile($profileId);
            $product = $this->getDbProduct($productId);

            Mailer::getInstance()->sendNotificationBuyProMembership($profile, $product);

            TopMessage::addInfo('SkinActProMembership mail send successfully');

            $this->updateProfileLastProMembershipEmailDate($profile);

            if ($isOpeningPopup) {
                $this->setSilenceClose(true);
            }
        } else {
            TopMessage::addError('SkinActProMembership mail not sent something went wrong');
        }
    }

    protected function updateProfileLastProMembershipEmailDate(Profile $profile): void
    {
        $sendMailTime = Converter::time();
        $profile->setLastProMembershipEmail($sendMailTime);
        $profile->update();
    }

    public function getDbProduct($productId)
    {
        return $this->executeCachedRuntime(function() use ($productId) {
            return Database::getRepo(Product::class)
                ->findOneBy(['product_id' => $productId]);
        }, [
            __METHOD__,
            self::class,
            $productId
        ]);
    }

    public function getDbProfile($profileId)
    {
        return $this->executeCachedRuntime(function() use ($profileId) {
            return Database::getRepo(Profile::class)
                ->findOneBy(['profile_id' => $profileId]);
        }, [__METHOD__,
            self::class,
            $profileId
        ]);
    }
}