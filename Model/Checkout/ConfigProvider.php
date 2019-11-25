<?php

namespace JustShout\GfsLandedCost\Model\Checkout;

use JustShout\GfsLandedCost\Helper\Config;
use JustShout\GfsLandedCost\Model\Gfs\Calculator;
use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Checkout Fee Config
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var Calculator
     */
    protected $_calculator;

    /**
     * ConfigProvider
     *
     * @param Config         $config
     * @param Calculator     $calculator
     */
    public function __construct(
        Config         $config,
        Calculator     $calculator
    ) {
        $this->_config = $config;
        $this->_calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $total = $this->_calculator->calculate();

        $config['landed_fee_label'] = __($this->_config->getLandedCostTotalLabel());
        $config['landed_fee_total'] = $total;
        $config['landed_fee_show'] = $this->_config->getLandedCostTotalLabel();

        return $config;
    }
}
