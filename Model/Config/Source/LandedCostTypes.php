<?php

namespace JustShout\GfsLandedCost\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Landed Cost Types
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class LandedCostTypes implements OptionSourceInterface
{
    const INCLUDED = 'included';

    const PAID_ON_DELIVERY = 'on_delivery';

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::INCLUDED,
                'label' => __('Included in Grand Totals')
            ],
            [
                'value' => self::PAID_ON_DELIVERY,
                'label' => __('Paid on Delivery')
            ]
        ];
    }
}