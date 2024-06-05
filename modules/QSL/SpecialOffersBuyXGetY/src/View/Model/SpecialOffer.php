<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\View\Model;

/**
 * Special offer view model
 */
class SpecialOffer extends \QSL\SpecialOffersBase\View\Model\ASpecialOffer
{
    /**
     * Returns schema fields for the Special Offer Conditions group.
     *
     * @return array
     */
    protected function getConditionFields()
    {
        return [
            'bxgyN' => [
                self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Integer',
                \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
                self::SCHEMA_LABEL      => 'Number of items to buy',
                self::SCHEMA_REQUIRED   => false,
                self::SCHEMA_WEIGHT     => 3010,
            ],
            'bxgyConditionCategories' => [
                self::SCHEMA_CLASS      => 'XLite\View\FormField\Select\Categories',
                self::SCHEMA_LABEL      => 'From these categories',
                self::SCHEMA_REQUIRED   => false,
                self::SCHEMA_WEIGHT     => 3520,
            ],
            'bxgyConditionMemberships' => [
                self::SCHEMA_CLASS      => 'QSL\SpecialOffersBuyXGetY\View\FormField\Select\SelectMemberships',
                self::SCHEMA_LABEL      => 'Eligible membership levels',
                self::SCHEMA_REQUIRED   => false,
                self::SCHEMA_WEIGHT     => 3530,
            ],
        ];
    }

    /**
     * Returns schema fields for the Special Offer Rewards group.
     *
     * @return array
     */
    protected function getRewardFields()
    {
        return [
            'bxgyM' => [
                self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\Integer',
                \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
                self::SCHEMA_LABEL      => 'Number of items to discount',
                self::SCHEMA_REQUIRED   => false,
                self::SCHEMA_WEIGHT     => 4010,
            ],
            'bxgyDiscount' => [
                self::SCHEMA_CLASS      => 'XLite\View\FormField\Input\Text\FloatInput',
                \XLite\View\FormField\Input\Text\FloatInput::PARAM_MIN => 0,
                self::SCHEMA_LABEL      => 'Special offer discount amount',
                self::SCHEMA_REQUIRED   => false,
                self::SCHEMA_WEIGHT     => 4020,
            ],
            'bxgyDiscountType' => [
                self::SCHEMA_CLASS      => 'QSL\SpecialOffersBuyXGetY\View\FormField\Select\DiscountType',
                self::SCHEMA_LABEL      => 'Special offer discount type',
                self::SCHEMA_REQUIRED   => false,
                self::SCHEMA_WEIGHT     => 4030,
            ],
        ];
    }

    /**
     * Initializes the default schema declaration.
     *
     * @return void
     */
    protected function defineSchemaDefault()
    {
        parent::defineSchemaDefault();

        $this->schemaDefault['bxgyPromoCategory'] = [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Checkbox\Enabled',
            self::SCHEMA_LABEL => 'Display short promo text and image on matching category pages',
            self::SCHEMA_REQUIRED => false,
            self::SCHEMA_WEIGHT => 6090,
        ];
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $newCategoryIds = isset($data['bxgyConditionCategories'])
            ? array_map('intval', $data['bxgyConditionCategories'])
            : [];

        unset($data['bxgyConditionCategories']);

        parent::setModelProperties($data);

        $entity = $this->getModelObject();
        $this->updateBxgyConditionCategories($entity, $newCategoryIds);
    }

    /**
     * Update condition categories
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer       Special offer
     * @param array                                                  $categoryIds List of IDs of new categories
     *
     * @return void
     */
    protected function updateBxgyConditionCategories($offer, $newCategoryIds)
    {
        $oldCategoryIds = [];
        $c = $offer->getBxgyConditionCategories();

        $oldCategoryOffers = $c ? $c->toArray() : [];
        if (!empty($oldCategoryOffers)) {
            $categoriesToDelete = [];

            foreach ($oldCategoryOffers as $co) {
                $oldCategoryIds[] = $co->getCategory()->getCategoryId();

                if (!in_array($co->getCategory()->getCategoryId(), $newCategoryIds)) {
                    // Add old category to the remove queue
                    $categoriesToDelete[] = $co;
                }
            }

            if ($categoriesToDelete) {
                // Remove links between product and old categories
                \XLite\Core\Database::getRepo('QSL\SpecialOffersBuyXGetY\Model\CategoryOffer')->deleteInBatch(
                    $categoriesToDelete
                );
            }
        }

        // Get list of category IDs which must be added
        $categoriesToAdd = array_diff($newCategoryIds, $oldCategoryIds);

        // Get list of categories (entities) from category IDs
        $categories = \XLite\Core\Database::getRepo('XLite\Model\Category')->findByIds($newCategoryIds);

        // Get list of category products
        $newCategoryOffers = $this->getCategoryOffers($offer, $categories, $categoriesToAdd);
        if ($newCategoryOffers) {
            // Update category products list
            \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\SpecialOffer')->update(
                $offer,
                ['bxgyConditionCategories' => $newCategoryOffers]
            );
        }
    }

    /**
     * Defines the category offers links collection
     *
     * @param \QSL\SpecialOffer\Model\SpecialOffer $offer       Special offer
     * @param array                                             $categories  Categories
     * @param array                                             $categoryIds Category IDs to filter categories
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getCategoryOffers($offer, $categories, $categoryIds)
    {
        $links = [];
        foreach ($categories as $category) {
            if (in_array($category->getCategoryId(), $categoryIds)) {
                $links[] = new \QSL\SpecialOffersBuyXGetY\Model\CategoryOffer(
                    [
                        'category' => $category,
                        'offer' => $offer,
                    ]
                );
            }
        }

        return new \Doctrine\Common\Collections\ArrayCollection($links);
    }

    /**
     * Retrieve property from the model object.
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        switch ($name) {
            case 'bxgyConditionCategories':
                $value = [];
                foreach (parent::getModelObjectValue($name) as $conditionCategory) {
                    $value[] = $conditionCategory->getCategory()->getCategoryId();
                }
                break;
            default:
                $value = parent::getModelObjectValue($name);
        }

        return $value;
    }

    /**
     * Returns list of multi-select array parameters.
     *
     * @return array
     */
    protected function getArrayFieldNames()
    {
        return array_merge(parent::getArrayFieldNames(), ['bxgyConditionMemberships']);
    }
}
