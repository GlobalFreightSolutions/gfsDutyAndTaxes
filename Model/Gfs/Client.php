<?php

namespace JustShout\GfsLandedCost\Model\Gfs;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use JustShout\Gfs\Helper\Config;
use JustShout\Gfs\Logger\Logger;
use JustShout\Gfs\Model\Gfs;
use JustShout\Gfs\Model\Gfs\Cookie\AccessToken;
use JustShout\GfsLandedCost\Helper;
use JustShout\GfsLandedCost\Model\Config\Source\Environment;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote;

/**
 * Landed Cost Client
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Client extends Gfs\Client
{
    /**
     * @var Helper\Config
     */
    protected $_landedCostConfig;

    /**
     * @var GuzzleClient
     */
    protected $_landedCostApi;

    /**
     * Client constructor
     *
     * @param Config        $config
     * @param Logger        $logger
     * @param AccessToken   $accessToken
     * @param Json          $json
     * @param Helper\Config $landedCostConfig
     */
    public function __construct(
        Config        $config,
        Logger        $logger,
        AccessToken   $accessToken,
        Json          $json,
        Helper\Config $landedCostConfig
    ) {
        $this->_landedCostConfig = $landedCostConfig;
        parent::__construct(
            $config,
            $logger,
            $accessToken,
            $json
        );
        $this->_landedCostApi = new GuzzleClient([
            'base_uri' => $this->_initLandedCostUri()
        ]);
    }

    /**
     * Send a request to GFS for a landing cost
     *
     * @param Quote $quote
     *
     * @return float
     *
     * @throws GuzzleException
     */
    public function calculate($quote)
    {
        $accessToken = base64_decode($this->getAccessToken());
        $json = $this->_getLandedCostData($quote);

        $response = $this->_landedCostApi->request('POST', 'api/dutyandtaxes/calculate', [
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ],
            'json' => $json,
        ]);

        $data = $this->_json->unserialize($response->getBody());

        return $data;
    }

    /**
     * Get the access token for a client
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        if (!$this->_accessToken->get()) {
            try {
                $response = $this->_identityClient->request('POST', 'connect/token', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'client_id'     => $this->_config->getRetailerId(),
                        'client_secret' => $this->_config->getRetailerSecret(),
                        'grant_type'    => 'client_credentials',
                        'scope'         => $this->_getAccessTokenScope(),
                    ]
                ]);
                $data = $this->_json->unserialize($response->getBody());
                $accessTokenString = isset($data['access_token']) ? $data['access_token'] : null;
                if (!$accessTokenString) {
                    throw new \Exception('Access token not available. Please check your credentials.');
                }

                $accessTokenString = base64_encode($accessTokenString);
                $this->_accessToken->set($accessTokenString);

                return $accessTokenString;
            } catch (\Exception $e) {
                $this->_logger->debug($e->getMessage());
                $this->_accessToken->delete();
            }
        }

        return $this->_accessToken->get();
    }

    /**
     * This method will setup the landed cost client
     *
     * @return string
     */
    protected function _initLandedCostUri()
    {
        if ($this->_landedCostConfig->getLandedCostEnvironment() === Environment::PRODUCTION) {
            return 'https://connect2.gfsdeliver.com/';
        } else {
            return 'https://sandbox.gfsdeliver.com/';
        }
    }

    /**
     * This method will generate the data object to send a landed cost request
     *
     * @param Quote $quote
     *
     * @return array
     */
    protected function _getLandedCostData($quote)
    {
        $data = [
            'orderId'      => $this->_getOrderId($quote),
            'deliveryDate' => $this->_getDeliveryDateEstimate($quote),
            'currencyCode' => $quote->getQuoteCurrencyCode(),
            'totalValue'   => (float) $quote->getSubtotal(),
            'from'         => $this->_getLandedCostFromAddress(),
            'to'           => $this->_getLandedCostToAddress($quote),
            'items'        => $this->_getLandedCostItems($quote)
        ];

        return $data;
    }

    /**
     * This method will get the order id
     *
     * @param Quote $quote
     *
     * @return string
     */
    protected function _getOrderId($quote)
    {
        return $this->_getDeliveryDateEstimate($quote) . '-' . $quote->getId();
    }

    /**
     * This method will get the delivery estimate
     *
     * @param Quote $quote
     *
     * @return string
     */
    protected function _getDeliveryDateEstimate($quote)
    {
        $date = new \DateTime();

        return $date->modify('+7 day')->format('Y-m-d');
    }

    /**
     * Get From Address
     *
     * @return array
     */
    protected function _getLandedCostFromAddress()
    {
        return $this->_landedCostConfig->getStoreAddress();
    }

    /**
     * Get To Address
     *
     * @param Quote $quote
     *
     * @return array
     */
    protected function _getLandedCostToAddress($quote)
    {
        $data = [];
        $shippingAddress = $quote->getShippingAddress();

        if (!empty($street = $shippingAddress->getData('street'))) {
            if (!is_array($street)) {
                $street = explode("\n", $street);
            }
            $data['address'] = implode(', ', $street);
        }

        if ($city = $shippingAddress->getCity()) {
            $data['city'] = $city;
        }

        if ($region = $this->_prepareRegion($shippingAddress)) {
            $data['region'] = $region;
        }

        if ($postCode = $this->_preparePostCode($shippingAddress->getPostcode())) {
            $data['postCode'] = $postCode;
        }

        if ($country = $shippingAddress->getCountryId()) {
            $data['countryCode'] = $country;
        } else {
            $data['countryCode'] = 'GB';
        }

        return $data;
    }

    /**
     * Get landed cost items
     *
     * @param Quote $quote
     *
     * @return array
     */
    protected function _getLandedCostItems($quote)
    {
        $items = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            /** @var Product $resourceModel */
            $resourceModel = $product->getResource();
            $hsCode = $resourceModel->getAttributeRawValue($product->getId(), $this->_landedCostConfig->getHarmonisedAttribute(), 0);
            $items[] = [
                'itemId'      => $item->getName(),
                'productCode' => $item->getSku(),
                'sku'         => $item->getSku(),
                'hsCode'      => $hsCode,
                'quantity'    => (int) $item->getQty(),
                'value'       => (float) $item->getRowTotalInclTax(),
            ];
        }

        return $items;
    }

    /**
     * Remove any letters from the postcode (i.e. US postcode prefixes like `NY 10111`)
     *
     * @param string $postCode
     *
     * @return string
     */
    protected function _preparePostCode($postCode)
    {
        return trim(preg_replace('/[^0-9]/', '', $postCode));
    }

    /**
     * Prepare the region
     *
     * @param Quote\Address $address
     *
     * @return string
     */
    protected function _prepareRegion($address)
    {
        $region = $address->getRegion();
        if (is_array($region)) {
            return $address->getCity();
        }

        if (!$region) {
            return $address->getCity();
        }

        return $region;
    }

    /**
     * Get Access Token Scope
     *
     * @return string
     */
    protected function _getAccessTokenScope()
    {
        $scope = 'checkout-api read';
        if ($this->_landedCostConfig->isCalculatorActive()) {
            $scope = 'checkout-api read landed-cost:read';
        }

        return $scope;
    }
}
