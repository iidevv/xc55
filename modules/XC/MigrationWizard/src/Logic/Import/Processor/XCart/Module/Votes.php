<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Reviews module
 */
class Votes extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Reviews
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return "pr.review_id AS `xc4EntityId`"
            . ", pr.review_id AS `reviewId`"
            . ", pr.message AS `review`"
            . ", pr.advantages AS `advantages`"
            . ", pr.disadvantages AS `disadvantages`"
            . ", pr.rating AS `rating`"
            . ", pr.datetime AS `additionDate`"
            . ", pr.userid AS `profile`"
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

        $time = time(); // since time is unknown use current

        return "( SELECT 2000000000 + prv.vote_id AS review_id"
                . ", null AS message"
                . ", null AS advantages"
                . ", null AS disadvantages"
                . ", null AS email"
                . ", vote_value AS rating"
                . ", {$time} AS datetime"
                . ", null AS userid"
                . ", null AS author"
                . ", 'A' AS status"
                . ", prv.productid AS productid"
                . ", prv.remote_ip AS remote_ip"
                . " FROM {$tp}product_votes AS prv ) as pr";
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT count(*)'
            . " FROM {$prefix}product_votes LIMIT 1"
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
}
