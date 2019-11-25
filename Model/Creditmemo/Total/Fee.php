<?php

namespace JustShout\GfsLandedCost\Model\Creditmemo\Total;

use JustShout\GfsLandedCost\Helper\Config;
use JustShout\GfsLandedCost\Model\Total;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

/**
 * Credit Memo Fee Total
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
     * @param Creditmemo $creditMemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditMemo)
    {
        $creditMemo->setData(Total\Fee::LANDED_FEE, 0);
        $creditMemo->setData(Total\Fee::BASE_LANDED_FEE, 0);

        $landedFee = $creditMemo->getOrder()->getData(Total\Fee::LANDED_FEE);
        $baseLandedFee = $creditMemo->getOrder()->getData(Total\Fee::BASE_LANDED_FEE);
        $creditMemo->setData(Total\Fee::LANDED_FEE, $landedFee);
        $creditMemo->setData(Total\Fee::BASE_LANDED_FEE, $baseLandedFee);

        if ($this->_config->isLandedCostIncluded()) {
            $creditMemo->setGrandTotal($creditMemo->getGrandTotal() + $landedFee);
            $creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal() + $baseLandedFee);
        } else {
            $creditMemo->setGrandTotal($creditMemo->getGrandTotal());
            $creditMemo->setBaseGrandTotal($creditMemo->getBaseGrandTotal());
        }

        return $this;
    }
}
