<?php

namespace JustShout\GfsLandedCost\Observer;

use JustShout\GfsLandedCost\Model\Gfs\Calculator;
use JustShout\GfsLandedCost\Model\Total\Fee;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

/**
 * Save Landing Fee Observer
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class SaveGfsLandedCostToOrderObserver implements ObserverInterface
{
    /**
     * Quote Repository
     *
     * @var CartRepositoryInterface
     */
    protected $_quoteRepository;

    /**
     * Calculator
     *
     * @var Calculator
     */
    protected $_calculator;

    /**
     * SaveGfsLandedCostToOrderObserver
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param Calculator              $calculator
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Calculator              $calculator
    ) {
        $this->_quoteRepository = $quoteRepository;
        $this->_calculator = $calculator;
    }

    /**
     * Set landing fee against order
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getOrder();
        try {
            /** @var Quote $quote */
            $quote = $this->_quoteRepository->get($order->getQuoteId());
        } catch (Exception\LocalizedException $e) {
            return $this;
        }

        $landedFee = $quote->getData(Fee::LANDED_FEE);
        $baseLandedFee = $quote->getData(Fee::BASE_LANDED_FEE);

        $order->setData(Fee::LANDED_FEE, $landedFee);
        $order->setData(Fee::BASE_LANDED_FEE, $baseLandedFee);

        $shipping = $quote->getShippingAddress();
        $quoteCountry = $shipping->getCountryId();
        $quotePostCode = $shipping->getPostcode();

        $cacheKey = $this->_calculator->getCacheKey($quotePostCode, $quoteCountry);
        $this->_calculator->invalidateCache($cacheKey);

        return $this;
    }
}
