<?php

namespace JustShout\GfsLandedCost\Block\Sales\Total;

use JustShout\GfsLandedCost\Helper\Config;
use JustShout\GfsLandedCost\Model\Total;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Block\Order\Totals;

/**
 * Total Fee Block
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Fee extends Template
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * Fee constructor
     *
     * @param Template\Context $context
     * @param Config           $config
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Config           $config,
        array            $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_config = $config;
    }

    /**
     * Setup Fee Total Row
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var Totals $parent */
        $parent = $this->getParentBlock();

        if (!$this->_config->getLandedCostTotalLabel()) {
            return $this;
        }

        if (!$parent->getSource()->getData(Total\Fee::LANDED_FEE)) {
            return $this;
        }

        $total = new DataObject([
            'code'  => Total\Fee::LANDED_FEE,
            'value' => $parent->getSource()->getData(Total\Fee::LANDED_FEE),
            'label' => $this->_config->getLandedCostTotalLabel(),
        ]);

        $parent->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
