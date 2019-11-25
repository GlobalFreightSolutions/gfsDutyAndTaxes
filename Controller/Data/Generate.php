<?php

namespace JustShout\GfsLandedCost\Controller\Data;

use JustShout\Gfs\Controller\Data;
use JustShout\Gfs\Model\Gfs\Request;
use JustShout\Gfs\Logger\Logger;
use JustShout\GfsLandedCost\Helper\Config;
use JustShout\GfsLandedCost\Model\Gfs\Calculator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Generate
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class Generate extends Data\Generate
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var Calculator
     */
    protected $_calculator;

    /**
     * Generate constructor
     *
     * @param Context      $context
     * @param JsonFactory  $jsonFactory
     * @param Request\Data $data
     * @param Logger       $logger
     * @param Config       $config
     * @param Calculator   $calculator
     */
    public function __construct(
        Context      $context,
        JsonFactory  $jsonFactory,
        Request\Data $data,
        Logger       $logger,
        Config       $config,
        Calculator   $calculator
    ) {
        $this->_config = $config;
        $this->_calculator = $calculator;
        parent::__construct(
            $context,
            $jsonFactory,
            $data,
            $logger
        );
    }

    /**
     * This method will generate the json object used for the request data in the  checkout widget
     *
     * @return Json
     */
    public function execute()
    {
        if (!$this->_config->isCalculatorActive()) {
            return parent::execute();
        }

        $result = $this->_jsonFactory->create();
        try {
            $data = $this->_data->getGfsData();
            $initialAddress = $this->_data->getInitialAddress();
            $landedCost = $this->_calculator->calculate();
            $landedCostIncluded = $this->_calculator->isLandedCostIncluded();
            $result = $this->_jsonFactory->create();
            $result->setData([
                'data'                 => $data,
                'initial_address'      => $initialAddress,
                'landed_cost'          => $landedCost,
                'landed_cost_included' => $landedCostIncluded
            ]);
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
            $result->setData([
                'data'                 => null,
                'initial_address'      => null,
                'landed_cost'          => 0,
                'landed_cost_included' => false
            ]);
            $result->setStatusHeader(401);
        }

        return $result;
    }
}
