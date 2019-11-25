<?php

namespace JustShout\GfsLandedCost\Block\Checkout;

use JustShout\Gfs\Block\Checkout\GfsProcessor;
use JustShout\Gfs\Helper\Config;
use JustShout\Gfs\Model\Gfs\Client;
use JustShout\GfsLandedCost\Helper\Config as GfsLandedConfig;

/**
 * Gfs Landed Layout Processor
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class GfsLandedProcessor extends GfsProcessor
{
    /**
     * Landed Config
     *
     * @var GfsLandedConfig
     */
    protected $_landedConfig;

    /**
     * GfsLandedProcessor constructor
     *
     * @param Client          $client
     * @param Config          $config
     * @param GfsLandedConfig $landedConfig
     */
    public function __construct(
        Client          $client,
        Config          $config,
        GfsLandedConfig $landedConfig
    ) {
        parent::__construct(
            $client,
            $config
        );
        $this->_landedConfig = $landedConfig;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        if (!$this->_isGfsActive()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['landed_fee']['component'] = 'JustShout_GfsLandedCost/js/view/checkout/cart/totals/fee';
        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['landed_fee']['sortOrder'] = '40';
        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']['children']['landed_fee']['config'] = [
            'template' => 'JustShout_GfsLandedCost/checkout/cart/totals/fee',
            'title'    => $this->_landedConfig->getLandedCostTotalLabel()
        ];

        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']['children']['details']['children']['subtotal']['component'] = 'Magento_Tax/js/view/checkout/summary/item/details/subtotal';

        return $jsLayout;
    }

    /**
     * Check if the landed cost calculator is active
     *
     * @return bool
     */
    protected function _isGfsActive()
    {
        return parent::_isGfsActive() && $this->_isCalculatorActive();
    }

    /**
     * Is Calculator active
     *
     * @return bool
     */
    protected function _isCalculatorActive()
    {
        return $this->_landedConfig->isCalculatorActive();
    }
}
