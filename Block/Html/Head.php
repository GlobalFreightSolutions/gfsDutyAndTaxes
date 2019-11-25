<?php

namespace JustShout\GfsLandedCost\Block\Html;

use JustShout\Gfs\Block\Html;
use JustShout\Gfs\Helper\Config;
use JustShout\Gfs\Model\Gfs\Client;
use JustShout\GfsLandedCost\Helper\Config as GfsLandedConfig;
use JustShout\GfsLandedCost\Model\Gfs\Client as GfsLandedClient;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

/**
 * Head Block
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Head extends Html\Head
{
    /**
     * Landed Config
     *
     * @var GfsLandedClient
     */
    protected $_landedConfig;

    /**
     * Landed Cost
     *
     * @var GfsLandedClient
     */
    protected $_landedCost;

    /**
     * Head constructor.
     *
     * @param Template\Context $context
     * @param Client           $client
     * @param Config           $config
     * @param Json             $json
     * @param GfsLandedConfig  $landedConfig
     * @param GfsLandedClient  $landedCost
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Client           $client,
        Config           $config,
        Json             $json,
        GfsLandedConfig  $landedConfig,
        GfsLandedClient  $landedCost,
        array            $data = []
    ) {
        parent::__construct(
            $context,
            $client,
            $config,
            $json,
            $data
        );
        $this->_landedConfig = $landedConfig;
        $this->_landedCost = $landedCost;
    }

    /**
     * Is Calculator active
     *
     * @return bool
     */
    public function isCalculatorActive()
    {
        return $this->_landedConfig->isCalculatorActive();
    }

    /**
     * Get Access token from landed cost module if enabled
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        if ($this->_landedConfig->isCalculatorActive()) {
            return $this->_landedCost->getAccessToken();
        } else {
            return parent::getAccessToken();
        }
    }
}
