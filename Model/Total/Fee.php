<?php

namespace JustShout\GfsLandedCost\Model\Total;

use JustShout\GfsLandedCost\Helper\Config;
use JustShout\GfsLandedCost\Model\Gfs\Calculator;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

/**
 * Landing Fee
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Fee extends Address\Total\AbstractTotal
{
    const LANDED_FEE = 'landed_fee';

    const BASE_LANDED_FEE = 'base_landed_fee';

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var Calculator
     */
    protected $_calculator;

    /**
     * Cost constructor.
     *
     * @param Config     $config
     * @param Calculator $calculator
     */
    public function __construct(
        Config     $config,
        Calculator $calculator
    ) {
        $this->_config = $config;
        $this->_calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     *
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total               $total
     *
     * @return $this
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Address\Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $landedFee = $this->_calculator->calculate();

        $total->setTotalAmount(self::LANDED_FEE, $landedFee);
        $total->setBaseTotalAmount(self::LANDED_FEE, $landedFee);
        $total->setLandedFee($landedFee);
        $total->setBaseLandedFee($landedFee);
        
        $quote->setLandedFee($landedFee);
        $quote->setBaseLandedFee($landedFee);

        if ($this->_config->isLandedCostIncluded()) {
            $quote->setGrandTotal($total->getGrandTotal() + $landedFee);
            $quote->setBaseGrandTotal($total->getBaseGrandTotal() + $landedFee);
        } else {
            $totals = array_sum($total->getAllTotalAmounts()) - $landedFee;
            $baseTotals = array_sum($total->getAllBaseTotalAmounts()) - $landedFee;
            $quote->setGrandTotal($totals);
            $quote->setBaseGrandTotal($baseTotals);
        }

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote         $quote
     * @param Address\Total $total
     *
     * @return array
     */
    public function fetch(Quote $quote, Address\Total $total)
    {
        $landedFee = $this->_calculator->calculate();

        $result = [
            'code'  => self::LANDED_FEE,
            'title' => $this->getLabel(),
            'value' => $landedFee
        ];

        return $result;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return __($this->_config->getLandedCostTotalLabel());
    }
}
