<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\Repo\Product;

class Tab extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const SEARCH_PRODUCT = 'product';
    public const P_POSITION     = 'position';

    // {{{ Search

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_object($value)) {
            $queryBuilder->andWhere('t.product = :product')
                ->setParameter('product', $value)
                ->orderBy('t.position');
        }
    }

    /**
     * @param \XC\CustomProductTabs\Model\Product\Tab $tab
     *
     * @return string
     */
    public function generateTabLink(\XC\CustomProductTabs\Model\Product\Tab $tab)
    {
        $result = $link = preg_replace(
            '/[^a-z0-9-_]/i',
            '',
            str_replace(
                ' ',
                '_',
                \XLite\Core\Converter::convertToTranslit($tab->getName())
            )
        );

        $i = 1;
        while (!$this->checkLinkUniqueness($result, $tab)) {
            $result = $link . '_' . $i;
            $i++;
        }

        return $result;
    }

    /**
     * @param string                                               $link
     * @param \XC\CustomProductTabs\Model\Product\Tab $tab
     *
     * @return bool
     */
    public function checkLinkUniqueness($link, \XC\CustomProductTabs\Model\Product\Tab $tab)
    {
        $result = !$this->findOneBy([
                'link'    => $link,
                'product' => $tab->getProduct(),
            ]) && !\XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->findOneBy([
                'link' => $link,
            ]) && !\XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->findOneBy([
                'service_name' => $link,
            ]);

        return $result;
    }


    // }}}

    public function loadFixture(array $record, \XLite\Model\AEntity $parent = null, array $parentAssoc = [])
    {
        $entity = parent::loadFixture($record, $parent, $parentAssoc);

        if (
            !$entity->isGlobal()
            && !$entity->getLink()
        ) {
            $entity->setLink(
                \XLite\Core\Database::getRepo('\XC\CustomProductTabs\Model\Product\Tab')
                    ->generateTabLink($entity)
            );
        }

        return $entity;
    }
}
