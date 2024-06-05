<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Customer;

/**
 * Wishlist products page controller
 */
class Wishlist extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check if the form ID validation is needed
     *
     * @return boolean
     */
    protected function isActionNeedFormId()
    {
        return parent::isActionNeedFormId() || ($this->getAction() === 'send');
    }

    public static function needFormId()
    {
        return \XLite\Core\Request::getInstance()->action === 'send';
    }

    public function getFormId()
    {
        return \XLite::getFormId();
    }

    public function isWishlistClose()
    {
        $auth = \XLite\Core\Auth::getInstance();
        $list = $this->getWishlist();

        return $auth->isWishlistAvailable() && $list->hasAccessToManage($auth->getProfile());
    }

    /**
     * @return boolean
     */
    public function isTitleVisible()
    {
        return false;
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Wishlist');
    }

    /**
     * @return string
     */
    public function getTitleString()
    {
        $list = $this->getWishlist();
        $name = static::t('Private wishlist title') . " ";

        if ($this->isAllowToShowWishlist($list)) {
            if ($userName = $list->getCustomer()->getName(false)) {
                $name = "{$userName}'s ";
            } else {
                $name = '';
            }
        }

        $count = $list->getProductsCount();

        $title = static::t('wishlist - X items', [
            'name'  => $name,
            'count' => $count,
        ]);

        return $name ? $title : ucfirst($title);
    }

    /**
     * Check if the wishlist is empty
     *
     * @return boolean
     */
    public function doesWishlistHaveProducts()
    {
        return $this->getWishlist()->hasProducts();
    }

    /**
     * Common method to determine current location
     *
     * @return array
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * We redirect not logged in user to login page
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        $edit = \XLite\Core\Auth::getInstance()->isWishlistAvailable() && $this->isAllowToManageWishlist($this->getWishlist());
        $show = $this->isAllowToShowWishlist();

        if (!($edit || $show)) {
            $this->redirect($this->buildURL('login'));
        }
    }

    /**
     * Send wishlist via email
     *
     * @return void
     */
    protected function doActionSend()
    {
        $request = \XLite\Core\Request::getInstance();
        $email   = $request->email;

        $result = false;

        if (
            $email
            && $this->isAllowToManageWishlist($this->getWishlist())
        ) {
            $result = \XLite\Core\Mailer::sendWishlist($email, $this->getWishlist());

            echo($result ? 'sent' : 'not-sent');
        } else {
            echo('not-allowed-sent');
        }

        if ($result) {
            \XLite\Core\TopMessage::addInfo('You have sent your wishlist to {{email}}', ['email' => $email]);
        } else {
            \XLite\Core\TopMessage::addError('Failed to send message to {{email}}', ['email' => $email]);
        }

        $this->translateTopMessagesToHTTPHeaders();
        \XLite::getInstance()->sendResponse();

        exit(0);
    }

    /**
     * Remove wishlist link procedure
     *
     * @return void
     */
    protected function doActionRemove()
    {
        $request = \XLite\Core\Request::getInstance();

        $linkId = $request->wishlist_link_id;

        // Only owners of wishlist is allow to manage it
        if ($this->isAllowToManageWishlist($this->getWishlist())) {
            if ($linkId) {
                $this->getWishlist()->removeWishlistLink($linkId);
            }

            \XLite\Core\Database::getEM()->flush();
        }

        $this->afterAction();
    }

    /**
     * Procedure to add product to wishlist
     *
     * @return void
     */
    protected function doActionAddToWishlist()
    {
        if ($this->getWishlistProduct()) {
            if (!\XLite\Core\Auth::getInstance()->isWishlistAvailable()) {
                $this->addPostponed($this->getWishlistProductId());
            } else {
                $this->processWishlistAddProduct($this->getWishlistProduct());
            }

            $this->afterAction();

            \XLite\Core\Event::updateWishlistButtonText(['text' => static::t('Added to wishlist')]);
        }

        $this->setSilenceClose();
    }

    /**
     * Generate link to wishlist
     *
     * @return void
     */
    protected function doActionCopyLink()
    {
        $list = $this->getWishlist();

        if ($list) {
            $hash = $list->getHash();

            if (!$hash) {
                $hash = $list->generateHash();

                \XLite\Core\Database::getEM()->flush();
            }

            $this->printAJAX([
                'base'      => \XLite\Core\URLManager::getShopURL(),
                'target'    => 'wishlist',
                'list_hash' => $hash,
            ]);
            $this->setSuppressOutput(true);

            \XLite\Core\TopMessage::addInfo('Copied');
        }
    }

    protected function addPostponed($productId)
    {
        $postponed = \XLite\Core\Session::getInstance()->postponedWishlistProducts;

        if (!$postponed || !is_array($postponed)) {
            $postponed = [];
        }

        $postponed[0] = $productId;

        \XLite\Core\Session::getInstance()->postponedWishlistProducts = $postponed;
    }

    /**
     * After action
     *
     * @return void
     */
    protected function afterAction()
    {
        $data = [
            'count' => \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist()
                ? \QSL\MyWishlist\Core\Wishlist::getInstance()->getWishlist()->getProductsCount()
                : 0,
        ];

        \XLite\Core\Event::updateWishlistProductsCount($data);
    }

    /**
     * Define the wishlist product id from request
     *
     * @return string
     */
    protected function getWishlistProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id;
    }

    /**
     * The product model from request
     *
     * @return \XLite\Model\Product
     */
    protected function getWishlistProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getWishlistProductId());
    }
}
