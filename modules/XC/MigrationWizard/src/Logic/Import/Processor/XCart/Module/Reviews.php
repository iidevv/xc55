<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Reviews module
 */
class Reviews extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'reviewId' => [
                static::COLUMN_IS_KEY => true,
            ],
            'review' => [],
            'advantages' => [],
            'disadvantages' => [],
            'rating' => [],
            'additionDate' => [],
            'profile' => [],
            'email' => [],
            'reviewerName' => [],
            'status' => [],
            'product' => [],
            'ip' => [],

            'xc4EntityId' => [],
        ];
    }

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     */
    public function __construct(\XLite\Logic\Import\Importer $importer)
    {
        parent::__construct($importer);

        // Update metadata to use custom ID value
        static::updateMetadata();
    }

    /**
     * Update entities metadata
     */
    public static function updateMetadata()
    {
        if (class_exists('XC\Reviews\Model\Review')) {
            $metadata = \XLite\Core\Database::getEM()->getClassMetadata('XC\Reviews\Model\Review');
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        }
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XC\Reviews\Model\Review');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $profileSet = ", pr.userid AS `profile`";
        if (
            Configuration::isModuleEnabled(Configuration::MODULE_ADVANCED_CUSTOMER_REVIEW)
            && !static::isTableColumnExists('product_reviews', 'userid')
        ) {
            $profileSet = ", pr.login AS `profile`";
        }

        return "pr.review_id AS `xc4EntityId`"
            . ", pr.review_id AS `reviewId`"
            . ", pr.message AS `review`"
            . ", pr.advantages AS `advantages`"
            . ", pr.disadvantages AS `disadvantages`"
            . ", pr.rating AS `rating`"
            . ", pr.datetime AS `additionDate`"
            . $profileSet
            . ", pr.email AS `email`"
            . ", pr.author AS `reviewerName`"
            . ", pr.status AS `status`"
            . ", pr.productid AS `product`"
            . ", pr.remote_ip AS `ip`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();

        if (Configuration::isModuleEnabled(Configuration::MODULE_ADVANCED_CUSTOMER_REVIEW)) {
            // has same data in single table
            return "{$tp}product_reviews AS pr";
        }

        $time = time(); // since time is unknown use current

        return "( SELECT 1000000000 + pr.review_id AS review_id"
            . ", pr.message AS message"
            . ", '' AS advantages"
            . ", '' AS disadvantages"
            . ", pr.email AS email"
            . ", null AS rating"
            . ", {$time} AS datetime"
            . ", null AS userid"
            . ", IF(INSTR('@', pr.email) > 0, null, pr.email) AS author" // if not an email show value as author
            . ", 'A' AS status"
            . ", pr.productid AS productid"
            . ", pr.remote_ip AS remote_ip"
            . " FROM {$tp}product_reviews AS pr ) AS pr";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result = "pr.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['XC\Reviews'];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'rating' value
     *
     * @param mixed $value Value
     *
     * @return float
     */
    protected function normalizeRatingValue($value)
    {
        if (empty($value)) {
            $prefix = self::getTablePrefix();

            if (
                self::getConnection()
                && (
                    $query = self::getConnection()->query(
                        "SELECT vote_value"
                        . " FROM {$prefix}product_votes"
                        . " WHERE productid = '{$this->currentRowData['product']}'"
                            . " AND remote_ip = '{$this->currentRowData['ip']}'"
                        . " GROUP BY productid"
                    )
                )
                && !empty($query)
            ) {
                $value = $query->fetchColumn();
            }
        }

        if (!$value) {
            $value = 100;
        }

        $value = (int)$value;
        if ($value <= 5 && $value >= 1) {
            $value = $value * 20;
        }

        return $value / 20;
    }

    /**
     * Normalize 'status' value
     *
     * @param mixed $value Value
     *
     * @return integer
     */
    protected function normalizeStatusValue($value)
    {
        return $value === 'A'
            ? \XC\Reviews\Model\Review::STATUS_APPROVED
            : \XC\Reviews\Model\Review::STATUS_PENDING;
    }

    /**
     * Normalize 'profile' value
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

            if (
                !$entity
                && Configuration::isModuleEnabled(Configuration::MODULE_ADVANCED_CUSTOMER_REVIEW)
                && !static::isTableColumnExists('product_reviews', 'userid')
            ) {
                // Only For Old Versions Where Login Field Was Used
                $entity = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($value);
            }

            return $entity;
        }, ['normalizeProfileValue', $value]);
    }

    /**
     * Normalize 'product' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Product
     */
    protected function normalizeProductValue($value)
    {
        return $this->executeCachedRuntime(static function () use ($value) {
            $entry = static::getEntryFromRegistryByClassAndSourceId('XLite\Model\Product', $value);

            return $entry ? \XLite\Core\Database::getRepo('XLite\Model\Product')->find($entry->getResultId()) : \XLite\Core\Database::getRepo('XLite\Model\Product')->find($value);
        }, ['normalizeProductValue', $value]);
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}product_reviews LIMIT 1"
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
        return static::t('Migrating reviews');
    }


    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function createModel(array $data)
    {
        return $this->getRepository()->insert(['id' => $data['reviewId']], false);
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        return \XLite\Core\Database::getRepo('\XC\Reviews\Model\Review')->find($data['reviewId']);
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
        if (!$this->normalizeProductValue($data['product'])) {
            return false;
        }

        if (empty($data['profile'])) {
            $data['profile'] = $data['email'];
        }

        if (!empty($data['advantages'])) {
            $data['review'] .= PHP_EOL . PHP_EOL . $data['advantages'];
        }
        if (!empty($data['disadvantages'])) {
            $data['review'] .= PHP_EOL . PHP_EOL . $data['disadvantages'];
        }

        unset($data['email']);
        unset($data['advantages']);
        unset($data['disadvantages']);

        return parent::importData($data);
    }

    /**
     * Import 'reviewId' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importReviewIdColumn($model, $value, array $column)
    {
    }
}
