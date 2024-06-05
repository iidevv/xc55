<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XLite\InjectLoggerTrait;

/**
 * Wishlist module
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */
class Wishlists extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    use InjectLoggerTrait;

    protected $_wishlists = [];
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'parentProduct' => [
                static::COLUMN_IS_KEY => true,
            ],
            'profile' => [],
            'wishlist' => [
                static::COLUMN_IS_KEY => true,
            ],
            'xc4EntityId' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\WishlistLink');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        if (static::isTableColumnExists('wishlist', 'userid')) {
            $profileSet = "CONCAT(wl.userid, '|', wl.productid) AS `xc4EntityId`, wl.userid AS `profile`";
        } else {
            $profileSet = "CONCAT(wl.login, '|', wl.productid) AS `xc4EntityId`, wl.login AS `profile`";
        }

        return $profileSet
            . ", wl.productid AS `parentProduct`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();
        return "{$tp}wishlist AS wl";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        if (static::isTableColumnExists('wishlist', 'userid')) {
            $result = 'wl.userid > 0';
        } else {
            $result = '1';
        }

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result = "wl.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    /**
     * Define Filter SQL. See ImportData Call
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        if (static::isTableColumnExists('wishlist', 'userid')) {
            return ['wl.userid'];
        } else {
            return ['wl.login'];
        }
    }

    /**
     * Define Fields That Will Be Used For Count
     *
     * @return string
     */
    public static function defineCountFields()
    {
        if (static::isTableColumnExists('wishlist', 'userid')) {
            return 'COUNT(DISTINCT wl.userid)';
        } else {
            return 'COUNT(DISTINCT wl.login)';
        }
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['QSL\MyWishlist'];
    }

    /**
     * Return TRUE if processor has a heading row
     *
     * @return boolean
     */
    public static function hasHeadingRow()
    {
        return count(static::defineSubProcessors()) === 0;
    }


    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'Profile' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Profile
     */
    protected function normalizeProfileValue($value)
    {
        return $this->executeCachedRuntime(static function () use ($value) {
            $entity = null;
            $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Profile', $value);

            $entity = $entry ? \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($entry->getResultId()) : null;

            if (!$entity) {
                if (static::isTableColumnExists('wishlist', 'userid')) {
                    $entity = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($value);
                } else {
                    // Only For Old Versions Where Login Field Was Used
                    $entity = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($value);
                }
            }

            return $entity;
        }, ['NormalizeProfileValue', $value]);
    }

    /**
     * Normalize 'ParentProduct' Value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Product
     */
    protected function normalizeParentProductValue($value)
    {
        return $this->executeCachedRuntime(static function () use ($value) {
            $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Product', $value);

            return $entry ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($entry->getResultId()) : \XLite\Core\Database::getRepo('XLite\Model\Product')->find($value);
        }, ['NormalizeProductValue', $value]);
    }

    /**
     * Normalize 'Wishlist' Value
     *
     * @param mixed $value Value
     *
     * @return \QSL\MyWishlist\Model\Wishlist
     */
    protected function normalizeWishlistValue($value)
    {
        if (empty($value)) {
            return null;
        }

        foreach ($this->_wishlists as $_p => $w) {
            if ($w->getId() == $value) {
                return $w;
            }
        }
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();
        if (static::isTableColumnExists('wishlist', 'userid')) {
            $result = 'userid > 0';
        } else {
            $result = '1';
        }

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}wishlist WHERE $result LIMIT 1"
        );
    }

    // }}} </editor-fold>

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migration wishlists');
    }


    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $_profile = $this->normalizeProfileValue($data['profile']);
        $_product = $this->normalizeParentProductValue($data['parentProduct']);
        if (!$_profile || !$_product || !$_profile->getProfileId() || !$_product->getProductId()) {
            $this->getLogger('migration_errors')->debug('', ['processor' => get_called_class(), 'error' => 'Product/Profile not found', 'data' => $data]);
            return false;
        }

        // As The Sql Set Is Sorted By Userid We Can Use Static Cache
        $_profile_id = $_profile->getProfileId();
        if (empty($this->_wishlists[$_profile_id])) {
            $this->_wishlists = [];// Clear Prev User
            $this->_wishlists[$_profile_id] = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\Wishlist')->findOneBy(['customer' => $_profile]);

            if (!$this->_wishlists[$_profile_id]) {
                $this->_wishlists[$_profile_id] = new \QSL\MyWishlist\Model\Wishlist();
                $this->_wishlists[$_profile_id]->setCustomer($_profile);
                \XLite\Core\Database::getEM()->persist($this->_wishlists[$_profile_id]);
                \XLite\Core\Database::getEM()->flush($this->_wishlists[$_profile_id]);// We Don't Know How Many Products For The User
            }
        }

        // Plain Values Here to Work Update Model Properly
        $data['wishlist'] = $this->_wishlists[$_profile_id]->getId();
        $data['parentProduct'] = $_product->getProductId();

        unset($data['profile']);
        $res = parent::importData($data);

        if (
            $this->currentlyProcessingModel
            && $this->currentlyProcessingModel instanceof \QSL\MyWishlist\Model\WishlistLink
        ) {
            if (!($this->_wishlists[$_profile_id]->getWishlistLink($_product))) {
                $this->currentlyProcessingModel->createSnapshot($_product);
                $this->_wishlists[$_profile_id]->addWishlistLinks($this->currentlyProcessingModel);
            }
        } else {
            $this->getLogger('migration_errors')->debug('', ['Processor' => get_class($this), 'Data' => $data, 'error' => 'For some reason the model is not WishlistLink']);
        }

        return $res;
    }

    /**
     * Import 'Profile' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importProfileColumn($model, $value, array $column)
    {
    }
}
