<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command\CSV;

class Reviews extends AFile implements IFile
{
    protected function getFilename(): string
    {
        return 'reviews_to_yotpo';
    }

    public function getCSVHeader(): array
    {
        return [
            'product_id',
            'product_title',
            'product_url',
            'date',
            'review_content',
            'review_score',
            'display_name',
            'email',
            'md_customer_country',
        ];
    }
}
