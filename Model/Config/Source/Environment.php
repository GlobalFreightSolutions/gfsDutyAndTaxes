<?php

namespace JustShout\GfsLandedCost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Environment
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Environment implements OptionSourceInterface
{
    const SANDBOX = 'sandbox';
    const PRODUCTION = 'production';

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SANDBOX,
                'label' => __('Sandbox')
            ],
            [
                'value' => self::PRODUCTION,
                'label' => __('Production')
            ]
        ];
    }
}