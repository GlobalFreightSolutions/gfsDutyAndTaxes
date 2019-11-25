<?php

namespace JustShout\GfsLandedCost\Observer;

use JustShout\GfsLandedCost\Model\Gfs\Calculator;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Invalidate Duty Fee Cache
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class InvalidateDutyFeeCache implements ObserverInterface
{
    /**
     * Checkout Session Factory
     *
     * @var SessionFactory
     */
    protected $_sessionFactory;

    /**
     * Duty Fee Calculator
     *
     * @var Calculator
     */
    protected $_calculator;

    /**
     * InvalidateDutyFeeCache constructor
     *
     * @param SessionFactory $sessionFactory
     * @param Calculator     $calculator
     */
    public function __construct(
        SessionFactory $sessionFactory,
        Calculator     $calculator
    ) {
        $this->_sessionFactory = $sessionFactory;
        $this->_calculator = $calculator;
    }

    /**
     * Invalidate duty fee session if items are added/updated
     *
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        /** @var Session $cart */
        $session = $this->_sessionFactory->create();
        $quote = $session->getQuote();

        $shipping = $quote->getShippingAddress();
        $quoteCountry = $shipping->getCountryId();
        $quotePostCode = $shipping->getPostcode();

        $cacheKey = $this->_calculator->getCacheKey($quotePostCode, $quoteCountry);
        $this->_calculator->invalidateCache($cacheKey);

        return $this;
    }
}
