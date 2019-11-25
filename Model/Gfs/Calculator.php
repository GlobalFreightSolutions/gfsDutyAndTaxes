<?php

namespace JustShout\GfsLandedCost\Model\Gfs;

use GuzzleHttp\Exception\GuzzleException;
use JustShout\Gfs\Logger\Logger;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote;
use JustShout\GfsLandedCost\Helper\Config;

/**
 * Calculator
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Calculator
{
    /**
     * @var SessionFactory
     */
    protected $_sessionFactory;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var Client
     */
    protected $_client;

    /**
     * @var CacheInterface
     */
    protected $_cache;

    /**
     * @var Json
     */
    protected $_json;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Calculator
     *
     * @param SessionFactory $sessionFactory
     * @param Config         $config
     * @param Client         $client
     * @param CacheInterface $cache
     * @param Registry       $registry
     * @param Json           $json
     * @param Logger         $logger
     */
    public function __construct(
        SessionFactory $sessionFactory,
        Config         $config,
        Client         $client,
        CacheInterface $cache,
        Registry       $registry,
        Json           $json,
        Logger         $logger
    ) {
        $this->_sessionFactory = $sessionFactory;
        $this->_config = $config;
        $this->_client = $client;
        $this->_cache = $cache;
        $this->_registry = $registry;
        $this->_json = $json;
        $this->_logger = $logger;
    }

    /**
     * This method will calculate the landed cost for the current quote
     *
     * @return float
     */
    public function calculate()
    {
        $quote = $this->_getQuote();
        if (!$quote->getData()) {
            return 0;
        }

        $shipping = $quote->getShippingAddress();
        $quoteCountry = $shipping->getCountryId();
        $quotePostCode = $shipping->getPostcode();
        if (!$quoteCountry || !$quotePostCode) {
            return 0;
        }

        $storeCountry = $this->_config->getStoreCountry();
        if ($quoteCountry === $storeCountry) {
            return 0;
        }

        if (!$this->_isAllowedLandedCountry($quoteCountry)) {
            return 0;
        }

        $cacheKey = $this->getCacheKey($quotePostCode, $quoteCountry);

        if ($this->_registry->registry($cacheKey) !== false && $this->_registry->registry($cacheKey) !== null) {
            return $this->_registry->registry($cacheKey);
        }

        $result = $this->_getCache($cacheKey);
        if ($result) {
            return $result;
        }

        try {
            $data = $this->_calculate();
            $result = isset($data['totalTaxCalculated']) ? (float) $data['totalTaxCalculated'] : 0.00;
            $this->_registry->register($cacheKey, $result);
            $result = $this->_json->serialize($result);
            $this->_setCache($cacheKey, $result);
        } catch (GuzzleException $e) {
            $this->_logger->info('Landed Cost Error');
            $this->_logger->info($e->getMessage());
            $result = 0.00;
        } catch (\Exception $e) {
            $this->_logger->info('Landed Cost Error');
            $this->_logger->info($e->getMessage());
            $result = 0.00;
        }

        return (float) $result;
    }

    /**
     * Get Cache Key
     *
     * @param string $quotePostCode
     * @param string $quoteCountry
     *
     * @return string
     */
    public function getCacheKey($quotePostCode, $quoteCountry)
    {
        $quotePostCode = preg_replace('/\s+/', '', $quotePostCode);
        $quoteCountry = preg_replace('/\s+/', '', $quoteCountry);

        return 'landing_fee_'. $quotePostCode . '_' . $quoteCountry;
    }

    /**
     * This method will check if landed cost is included in the grand total
     *
     * @return string
     */
    public function isLandedCostIncluded()
    {
        return $this->_config->isLandedCostIncluded() ? 'charge' : 'estimate';
    }

    /**
     * This method will calculate the landing cost
     *
     * @return float
     *
     * @throws GuzzleException
     */
    protected function _calculate()
    {
        $quote = $this->_getQuote();

        return $this->_client->calculate($quote);
    }

    /**
     * Get Quote
     *
     * @return Quote
     */
    protected function _getQuote()
    {
        /** @var Session $session */
        $session = $this->_sessionFactory->create();

        return $session->getQuote();
    }

    /**
     * Check if the country is applicable for landed costs
     *
     * @param string $country
     *
     * @return bool
     */
    protected function _isAllowedLandedCountry($country)
    {
        return in_array($country, $this->_config->getAllowedLandedCountries());
    }

    /**
     * Get Cached Result
     *
     * @param string $key
     *
     * @return array|string|bool
     */
    protected function _getCache($key)
    {
        $result = $this->_cache->load($key);

        return $result ? $this->_json->unserialize($result) : $result;
    }

    /**
     * Save Result to Cache
     *
     * @param string              $key
     * @param string|array|object $result
     *
     * @return void
     */
    protected function _setCache($key, $result)
    {
        if (is_array($result) || is_object($result)) {
            $result = $this->_json->serialize($result);
        }

        $this->_cache->save($result, $key, [], 7200);
    }

    /**
     * Invalidate Cache
     *
     * @param string $key
     */
    public function invalidateCache($key)
    {
        $this->_setCache($key, 0);
    }
}
