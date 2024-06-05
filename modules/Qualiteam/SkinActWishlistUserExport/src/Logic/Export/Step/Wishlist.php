<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\Logic\Export\Step;


use Qualiteam\SkinActWishlistUserExport\View\SearchPanel\Admin\WishlistSearch as SearchPanel;

class Wishlist extends \XLite\Logic\Export\Step\AStep
{
    // {{{ Data


    public function __construct(\XLite\Logic\Export\Generator $generator = null)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{SearchPanel::PARAM_SEARCH_NON_EMPTY_LISTS} = true;

        $this->getRepository()->setExportFilter($cnd);

        parent::__construct($generator);
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('\QSL\MyWishlist\Model\Wishlist');
    }


    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'wishlists.csv';
    }

    protected function getModelDatasets(\XLite\Model\AEntity $model)
    {
        return $this->distributeDatasetModel(
            parent::getModelDatasets($model),
            'wishlistLink',
            $model->getWishlistLinks()
        );
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'userEmail' => [],
            'firstName' => [],
            'lastName' => [],
            'productName' => [
                static::COLUMN_MULTIPLE => true,
            ],
            'qtyInStock' => [
                static::COLUMN_MULTIPLE => true,
            ],
        ];

        return $columns;
    }

    // }}}


    // {{{ Getters and formatters

    protected function getProductNameColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['wishlistLink']) ? '' : $dataset['wishlistLink']->getParentProduct()->getName();
    }

    protected function getQtyInStockColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['wishlistLink']) ? '' : $dataset['wishlistLink']->getParentProduct()->getQty();
    }

    protected function getUserEmailColumnValue(array $dataset, $name, $i)
    {
        $profile = $dataset['model']->getCustomer();
        $login = $profile->getLogin();

        return $login ?: 'Unknown';
    }

    protected function getFirstNameColumnValue(array $dataset, $name, $i)
    {
        $profile = $dataset['model']->getCustomer();

        $profileAddress = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        $firstName = 'Customer';

        if ($profileAddress) {
            $firstName = $profileAddress->getFirstname();
        }
        return $firstName;
    }

    protected function getLastNameColumnValue(array $dataset, $name, $i)
    {
        $profile = $dataset['model']->getCustomer();

        $profileAddress = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        $lastName = 'Customer';

        if ($profileAddress) {
            $lastName = $profileAddress->getLastname();
        }
        return $lastName;
    }
    // }}}
}
