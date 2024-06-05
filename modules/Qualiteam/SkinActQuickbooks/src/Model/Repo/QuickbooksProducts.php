<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrderErrors;

/**
 * The Product model repository
 */
class QuickbooksProducts extends \XLite\Model\Repo\ARepo
{
    /**
     * Delete quickbooks products
     * 
     * @param mixed $productIds
     * 
     * @return void
     */
    public function deleteProducts($productIds)
    {
        if (!empty($productIds)) {
            if (!is_array($productIds)) {
                $productIds = [$productIds];
            }
            $this->createQueryBuilder('qp')
                ->andWhere('qp.product_id in (:ids)')
                ->setParameter('ids', $productIds)
                ->delete()
                ->getQuery()
                ->execute();
        }
    }
    
    /**
     * Delete quickbooks product variant
     * 
     * @param mixed $productId
     * @param mixed $variantId
     * 
     * @return void
     */
    public function deleteVariant($productId, $variantId)
    {
        if (!empty($productId)) {
            $this->createQueryBuilder('qp')
                ->andWhere('qp.product_id = :pid')
                ->setParameter('pid', $productId)
                ->andWhere('qp.variant_id = :vid')
                ->setParameter('vid', $variantId)
                ->delete()
                ->getQuery()
                ->execute();
        }
    }
    
    /**
     * Get sync variants count of a product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function getProductSyncVariantsCount(\XLite\Model\Product $product)
    {
        $count = $this->createPureQueryBuilder('qp')
            ->select('COUNT(qp.variant_id)')
            ->andWhere('qp.product_id = :product')
            ->andWhere('qp.variant_id > 0')
            ->setParameter('product', $product)
            ->getSingleScalarResult();

        return (class_exists('XC\ProductVariants\Main')) ? $count : 0;
    }
    
    /**
     * Get synced products with updated prices
     *
     * @return array
     */
    public function getProductsWithUpdatedPrices()
    {
        $result = [];
        
        $xcVariantsModule = class_exists('XC\ProductVariants\Main');
        $vCondition = ($xcVariantsModule) ? ' AND qp.variant_id = 0' : '';
        
        $qb = $this->createPureQueryBuilder('qp');
        $qb->select('
            IDENTITY(qp.product_id) AS product_id,
            qp.variant_id
        ')->distinct();
        $qb->leftJoin(
            'XLite\Model\Product', 'p', 'WITH',
            "p.product_id = qp.product_id $vCondition AND qp.price != p.price"
        );
        $qb->andWhere('p.product_id IS NOT NULL');
        
        $result = $qb->getQuery()->getArrayResult();
        
        if ($xcVariantsModule) {
            $qbv = $this->createPureQueryBuilder('qp');
            $qbv->select('
                IDENTITY(qp.product_id) AS product_id,
                qp.variant_id
            ')->distinct();
            $qbv->leftJoin(
                'XLite\Model\Product', 'p', 'WITH',
                'p.product_id = qp.product_id'
            );
            $qbv->leftJoin(
                'XC\ProductVariants\Model\ProductVariant', 'pv', 'WITH',
                'pv.product = qp.product_id AND qp.variant_id = pv.id '
                . 'AND qp.price != (CASE pv.price WHEN 0 THEN p.price ELSE pv.price END)'
            );
            $qbv->andWhere('pv.product IS NOT NULL');
            
            $result = array_merge($result, $qbv->getQuery()->getArrayResult());
        }
        

        return $result;
    }
    
    /**
     * Check if record product_id/variant_id exists
     *
     * @param array [product_id, variant_id] IDs
     *
     * @return array
     */
    public function recordExists($ids)
    {
        if ($ids && is_array($ids)) {
            $ids = array_values($ids);
        }
        $result = $this->createPureQueryBuilder('qp')
            ->select('
                qp.quickbooks_fullname, qp.quickbooks_editsequence,
                qp.quickbooks_listid, qp.price
            ')
            ->andWhere('qp.product_id = :product_id')
            ->andWhere('qp.variant_id = :variant_id')
            ->setParameter('product_id', intval($ids[0]))
            ->setParameter('variant_id', intval($ids[1]))
            ->getSingleResult();

        return $result;
    }
    
    /**
     * Get "QuickBooks Item Name/Number" of a product/variant
     *
     * @param integer $product_id Product ID
     * @param integer $variant_id Variant ID
     *
     * @return string
     */
    public function getQuickbooksFullname($product_id, $variant_id = 0)
    {
        $result = $this->createPureQueryBuilder('qp')
            ->select('qp.quickbooks_fullname')
            ->andWhere('qp.product_id = :product_id')
            ->andWhere('qp.variant_id = :variant_id')
            ->setParameter('product_id', $product_id)
            ->setParameter('variant_id', $variant_id)
            ->getSingleScalarResult();

        return $result;
    }
    
    /**
     * Set "QuickBooks Item Name/Number" of a product/variant
     *
     * @param integer $product_id Product ID
     * @param integer $variant_id Variant ID
     * @param string  $value
     *
     * @return void
     */
    public function setQuickbooksFullname($product_id, $variant_id = 0, $value = '')
    {
        $table = $this->getTableName();
        
        $queryData = [
            'product_id'              => $product_id,
            'variant_id'              => $variant_id,
            'price'                   => 0.00,
            'quickbooks_fullname'     => $value,
            'quickbooks_listid'       => '',
            'quickbooks_editsequence' => '',
        ];
        
        $exists = $this->recordExists([$product_id, $variant_id]);
        
        // Check if there are some not synced orders with this product/variant
        // then reset errors for such orders
        
        if (
            $exists['quickbooks_fullname'] != $value
            && !empty($value)
            && ($errorOrders = Database::getRepo(QuickbooksOrderErrors::class)
                ->getOrderIdsByProduct($product_id, $variant_id))
        ) {
            Database::getRepo(QuickbooksOrderErrors::class)
                ->deleteOrdersErrors($errorOrders);
        }
        
        if ($exists) {
            
            $recordRemoved = false;
            
            if (empty($value)) {
                
                if (QuickbooksConnector::unlinkProductWhenFullnameEmpty()) {
                    
                    $this->deleteVariant($product_id, $variant_id);
                
                    $recordRemoved = true;
                }
            }
            
            if (!$recordRemoved) {
                
                $updateData = ['quickbooks_fullname = :quickbooks_fullname'];
                
                // Check if product/variant already synced
                // if quickbooks_fullname changed -> quickbooks_listid = ''
                // to re-sync it next time
                
                if (
                    !empty($exists['quickbooks_listid'])
                    && $exists['quickbooks_fullname'] != $value
                ) {
                    $updateData[] = 'quickbooks_listid = :quickbooks_listid';
                }
                
                $query = "UPDATE {$table} SET " . implode(', ', $updateData)
                       . " WHERE product_id = :product_id"
                       . " AND variant_id = :variant_id";

                Database::getEM()->getConnection()->executeQuery(
                    $query,
                    $queryData
                );
            }
            
        } elseif (!empty($value)) {
            
            $query = "INSERT IGNORE INTO {$table} ("
                   . "product_id, variant_id, price, quickbooks_fullname,"
                   . "quickbooks_editsequence, quickbooks_listid"
                   . ") VALUES ("
                   . ":product_id, :variant_id, :price, :quickbooks_fullname,"
                   . ":quickbooks_editsequence, :quickbooks_listid)";

            Database::getEM()->getConnection()->executeQuery(
                $query,
                $queryData
            );
        }
    }
    
    /**
     * Get product/variant data
     *
     * @param integer $product_id Product ID
     * @param integer $variant_id Variant ID
     *
     * @return array
     */
    public function getProductData($product_id, $variant_id = 0)
    {
        $product = Database::getRepo('XLite\Model\Product')
            ->find($product_id);
        
        if (!$product) return [];
        
        $result = [
            'sku'    => $product->getSku(),
            'name'   => $product->getName(),
            'price'  => $product->getPrice(),
            'amount' => $product->getAmount(),
        ];
        
        $xcVariantsModule = class_exists('XC\ProductVariants\Main');
        $xcVariantsRepo = 'XC\ProductVariants\Model\ProductVariant';
        if (!$xcVariantsModule) $variant_id = 0;

        $qb = $this->createPureQueryBuilder('qp');
        $qb->select('
            qp.quickbooks_editsequence,
            qp.quickbooks_listid,
            qp.quickbooks_fullname
            ')
            ->andWhere('qp.product_id = :product_id')
            ->andWhere('qp.variant_id = :variant_id')
            ->setParameter('product_id', $product_id)
            ->setParameter('variant_id', $variant_id);

        $pResult = $qb->getQuery()->getOneOrNullResult();
        
        if (!empty($pResult)) {
            $result = array_merge($result, $pResult);
        }
            
        if (!empty($variant_id)) {
            
            $variant = Database::getRepo($xcVariantsRepo)
                ->findOneBy(['product' => $product_id, 'id' => $variant_id]);
            
            if ($variant) {
                if ($variant->getPrice() && !$variant->getDefaultPrice()) {
                    $result['price'] = $variant->getPrice();
                }

                if ($variant->getAmount() && !$variant->getDefaultAmount()) {
                    $result['amount'] = $variant->getAmount();
                }

                $result['sku'] = $variant->getSku() ?: $variant->getVariantId();

                $attrs = [];
                foreach ($product->getVariantsAttributes() as $attribute) {
                    if ($av = $variant->getAttributeValue($attribute)) {
                        $attrs[] = trim($attribute->getName())
                                 . ': ' . trim($av->asString());
                    }
                }
                if (!empty($attrs)) {
                    $result['name'] .= ' (' . implode(', ', $attrs) . ')';
                }
            }
            
        }

        return $result;
    }
}