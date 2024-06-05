<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\View\FormModel\Product;

use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    use FreqBoughtTogetherTrait;

    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema[$this->getDefaultDependSectionName()][$this->getExcludeFreqBoughtTogetherParamName()] = [
            'label'    => $this->getExcludeFreqBoughtTogetherParamLabel(),
            'type'     => $this->getExcludeFreqBoughtTogetherParamInputType(),
            'position' => $this->getCustomFreqBoughtTogetherSectionPosition() ?? $this->getDefaultFreqBoughtTogetherSectionPosition(),
        ];

        return $this->correctFreqBoughtTogetherSectionPosition($schema);
    }

    /**
     * Correct exclude frequently bought together section position if isset dependent section
     *
     * @param array $schema
     *
     * @return array
     */
    protected function correctFreqBoughtTogetherSectionPosition(array $schema): array
    {
        $dependSection = $this->getDefaultDependSectionForExcludeFreqBoughtTogetherSection($schema);

        if (isset($dependSection)) {
            $schema[$this->getDefaultDependSectionName()][$this->getExcludeFreqBoughtTogetherParamName()]['position']
                = $dependSection['position'] + $this->getExcludeFreqBoughtTogetherParamDependPositionStep();
        }

        return $schema;
    }

    /**
     * Get default exclude frequently bought together section position
     *
     * @return int
     */
    protected function getDefaultFreqBoughtTogetherSectionPosition(): int
    {
        return 605;
    }

    /**
     * Get custom exclude frequently bought together section position
     *
     * @return int|null
     */
    protected function getCustomFreqBoughtTogetherSectionPosition(): ?int
    {
        return null;
    }

    /**
     * Get dependent section and param name on "exclude frequently bought together" section
     *
     * @param array $schema
     *
     * @return array|null
     */
    protected function getDefaultDependSectionForExcludeFreqBoughtTogetherSection(array $schema): ?array
    {
        return $schema[$this->getDefaultDependSectionName()][$this->getDefaultDependSectionParamName()];
    }

    /**
     * Get dependent section name
     *
     * @return string
     */

    protected function getDefaultDependSectionName(): string
    {
        return static::SECTION_DEFAULT;
    }

    /**
     * Get dependent section param name
     *
     * @return string
     */
    protected function getDefaultDependSectionParamName(): string
    {
        return 'full_description';
    }
}