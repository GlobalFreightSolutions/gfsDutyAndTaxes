<?php

namespace JustShout\GfsLandedCost\Ui\Component\Listing\Column;

use JustShout\GfsLandedCost\Helper\Config;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Ui\Component\Listing\Column\Price;

/**
 * Fee Column
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Fee extends Price
{
    /**
     * Config Helper
     *
     * @var Config
     */
    protected $_config;

    /**
     * Fee constructor
     *
     * @param ContextInterface       $context
     * @param UiComponentFactory     $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param Config                 $config
     * @param array                  $components
     * @param array                  $data
     */
    public function __construct(
        ContextInterface       $context,
        UiComponentFactory     $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        Config                 $config,
        array                  $components = [],
        array                  $data = []
    ) {
        $this->_config = $config;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $priceFormatter,
            $components,
            $data
        );
    }

    /**
     * This will set the column label dynamically with what is in the system config
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');

        $config['label'] = $this->_config->getLandedCostTotalLabel();
        $this->setData('config', $config);
    }
}
