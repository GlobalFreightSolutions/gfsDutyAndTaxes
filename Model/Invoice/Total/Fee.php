<?php

namespace JustShout\GfsLandedCost\Model\Invoice\Total;

use JustShout\GfsLandedCost\Helper\Config;
use JustShout\GfsLandedCost\Model\Total;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Invoice Fee Total
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Fee extends AbstractTotal
{
    protected $_config;

    public function __construct(
        Config $config,
        array $data = []
    ) {
        $this->_config = $config;
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     *
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $invoice->setData(Total\Fee::LANDED_FEE, 0);
        $invoice->setData(Total\Fee::BASE_LANDED_FEE, 0);

        $landedFee = $invoice->getOrder()->getData(Total\Fee::LANDED_FEE);
        $invoice->setData(Total\Fee::LANDED_FEE, $landedFee);
        $baseLandedFee = $invoice->getOrder()->getData(Total\Fee::BASE_LANDED_FEE);
        $invoice->setData(Total\Fee::BASE_LANDED_FEE, $baseLandedFee);

        if ($this->_config->isLandedCostIncluded()) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getData(Total\Fee::LANDED_FEE));
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getData(Total\Fee::BASE_LANDED_FEE));
        } else {
            $invoice->setGrandTotal($invoice->getGrandTotal());
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal());
        }

        return $this;
    }
}
