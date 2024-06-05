<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\FormField\Select;

/**
 * Select "Yes / No"
 */
class QuestionState extends \XLite\View\FormField\Select\Regular
{
    /**
     * Yes/No mode values
     */
    public const TYPE_PUBLISHED = 1;
    public const TYPE_MODERATED = 0;

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value === true || $value === '1' || $value === 1) {
            $value = static::TYPE_PUBLISHED;
        } elseif ($value === false || $value === '0' || $value === 0) {
            $value = static::TYPE_MODERATED;
        }

        return $value;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::TYPE_PUBLISHED => static::t('Published question'),
            static::TYPE_MODERATED  => static::t('Hidden question (under moderation)'),
        ];
    }
}
