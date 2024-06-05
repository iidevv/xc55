<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Configuration;

use XLite\Core\ConfigCell;

class ConfigurationBuilder
{
    /**
     * @var ConfigCell
     */
    protected $rawConfiguration;

    /**
     * Constructor
     *
     * @param ConfigCell $rawConfiguration
     *
     * @return void
     */
    public function __construct(ConfigCell $rawConfiguration)
    {
        $this->rawConfiguration = $rawConfiguration;
    }

    /**
     * Build
     *
     * @return Configuration
     * @noinspection PhpUndefinedFieldInspection
     */
    public function build(): Configuration
    {
        return new Configuration(
            (string)$this->rawConfiguration->yotpo_app_key,
            (string)$this->rawConfiguration->yotpo_secret_key,
            (bool)$this->rawConfiguration->yotpo_dev_mode,
            (string)$this->rawConfiguration->yotpo_product_prefix,
            (bool)$this->rawConfiguration->yotpo_show_review_widget,
            (bool)$this->rawConfiguration->yotpo_show_star_rating,
            (string)$this->rawConfiguration->yotpo_review_widget_id,
            (string)$this->rawConfiguration->yotpo_star_widget_id,
            (bool)$this->rawConfiguration->yotpo_conversion_tracking,
        );
    }
}