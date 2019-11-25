<?php

namespace JustShout\GfsLandedCost\Helper;

use JustShout\Gfs\Helper;
use JustShout\GfsLandedCost\Model\Config\Source\LandedCostTypes;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Config Helper
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Config extends Helper\Config
{
    /**
     * Active Config
     */
    const CONFIG_LANDED_COST_ACTIVE = 'carriers/gfs/landed_cost_active';

    /**
     * Environment
     */
    const CONFIG_LANDED_COST_ENVIRONMENT = 'carriers/gfs/landed_cost_environment';

    /**
     * Label Config
     */
    const CONFIG_LANDED_COST_LABEL = 'carriers/gfs/landed_cost_total_label';

    /**
     * Total Label Config
     */
    const CONFIG_LANDED_COST_TOTAL_SHOW = 'carriers/gfs/landed_cost_total_show';

    /**
     * Total Label Config
     */
    const CONFIG_LANDED_COST_TOTAL_LABEL = 'carriers/gfs/landed_cost_total_label';

    /**
     * Landed Cost Type
     */
    const CONFIG_LANDED_COST_TYPE = 'carriers/gfs/landed_cost_type';

    /**
     * Allowed Countries
     */
    const CONFIG_ALLOWED_LANDED_COUNTRIES = 'carriers/gfs/allowed_landed_countries';

    /**
     * Harmonised Code
     */
    const CONFIG_HARMONISED_ATTRIBUTE = 'carriers/gfs/harmonised_attribute';

    /**
     * Manufacturer Country Attribute
     */
    const CONFIG_MANUFACTURER_COUNTRY_ATTRIBUTE = 'carriers/gfs/manufacturer_country_attribute';

    /**
     * Is Landed Cost Calculator Active
     *
     * @return bool
     */
    public function isCalculatorActive()
    {
        if (!$this->isActive()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(self::CONFIG_LANDED_COST_ACTIVE, ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get Landed Cos tEnvironment
     *
     * @return bool
     */
    public function getLandedCostEnvironment()
    {
        return $this->scopeConfig->getValue(self::CONFIG_LANDED_COST_ENVIRONMENT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Landed Cost Total Label
     *
     * @return bool
     */
    public function getLandedCostTotalShow()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_LANDED_COST_TOTAL_SHOW, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Landed Cost Total Label
     *
     * @return string
     */
    public function getLandedCostTotalLabel()
    {
        return $this->scopeConfig->getValue(self::CONFIG_LANDED_COST_LABEL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if landed cost is included
     *
     * @return bool
     */
    public function isLandedCostIncluded()
    {
        $type = $this->scopeConfig->getValue(self::CONFIG_LANDED_COST_TYPE, ScopeInterface::SCOPE_STORE);
        if (!$type) {
            $type = LandedCostTypes::INCLUDED;
        }

        return $type === LandedCostTypes::INCLUDED;
    }

    /**
     * Get Allowed Landed Countries
     *
     * @return array
     */
    public function getAllowedLandedCountries()
    {
        $countries = $this->scopeConfig->getValue(self::CONFIG_ALLOWED_LANDED_COUNTRIES, ScopeInterface::SCOPE_STORE);
        $countries = explode(',', $countries);
        $countries = array_filter($countries);

        return $countries;
    }

    /**
     * Get Harmonised Attribute
     *
     * @return string
     */
    public function getHarmonisedAttribute()
    {
        return $this->scopeConfig->getValue(self::CONFIG_HARMONISED_ATTRIBUTE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Manufacturer Attribute
     *
     * @return string
     */
    public function getManufacturerAttribute()
    {
        return $this->scopeConfig->getValue(self::CONFIG_MANUFACTURER_COUNTRY_ATTRIBUTE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Store Country
     *
     * @return string
     */
    public function getStoreCountry()
    {
        return $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_COUNTRY_ID, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Store Origin Address
     *
     * @return array
     */
    public function getStoreAddress()
    {
        $data = [];
        $street = [];
        if ($addressOne = trim($this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS1, ScopeInterface::SCOPE_STORE))) {
            $street[] = $addressOne;
        }

        if ($addressTwo = trim($this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS2, ScopeInterface::SCOPE_STORE))) {
            $street[] = $addressTwo;
        }

        if (!empty($street)) {
            $data['address'] = implode(', ', $street);
        }

        if ($city = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_CITY)) {
            $data['city'] = $city;
        }

        if ($region = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_REGION_ID)) {
            $data['region'] = $region;
        }

        if ($postCode = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ZIP)) {
            $data['postCode'] = $postCode;
        }

        if ($country = $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_COUNTRY_ID)) {
            $data['countryCode'] = $country;
        } else {
            $data['countryCode'] = 'GB';
        }

        return $data;
    }
}
