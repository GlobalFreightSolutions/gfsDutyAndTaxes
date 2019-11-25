<?php

namespace JustShout\GfsLandedCost\Setup;

use JustShout\GfsLandedCost\Model\Total\Fee;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install Schema
 *
 * @package   JustShout\GfsLandedCost
 * @author    JustShout <http://developer.justshoutgfs.com/>
 * @copyright JustShout - 2019
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->_setupQuoteTable($setup);
        $this->_setupQuoteAddressTable($setup);
        $this->_setupOrderTable($setup);
        $this->_setupOrderGridTable($setup);
        $this->_setupInvoiceTable($setup);
        $this->_setupCreditMemoTable($setup);
        $setup->endSetup();
    }

    /**
     * This method will update the quote table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function _setupQuoteTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('quote'), Fee::LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Landed Fee'
        ]);

        $setup->getConnection()->addColumn($setup->getTable('quote'), Fee::BASE_LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Base Landed Fee'
        ]);
    }

    /**
     * This method will update the quote address table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function _setupQuoteAddressTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('quote_address'), Fee::LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Landed Fee'
        ]);

        $setup->getConnection()->addColumn($setup->getTable('quote_address'), Fee::BASE_LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Base Landed Fee'
        ]);
    }

    /**
     * This method will update the order table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function _setupOrderTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('sales_order'), Fee::LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Landed Fee'
        ]);

        $setup->getConnection()->addColumn($setup->getTable('sales_order'), Fee::BASE_LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Base Landed Fee'
        ]);
    }

    /**
     * This method will update the order grid table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function _setupOrderGridTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('sales_order_grid'), Fee::LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Landed Fee'
        ]);

        $setup->getConnection()->addColumn($setup->getTable('sales_order_grid'), Fee::BASE_LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Base Landed Fee'
        ]);
    }

    /**
     * This method will update the invoice table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function _setupInvoiceTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('sales_invoice'), Fee::LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Landed Fee'
        ]);

        $setup->getConnection()->addColumn($setup->getTable('sales_invoice'), Fee::BASE_LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Base Landed Fee'
        ]);
    }

    /**
     * This method will update the credit memo table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    protected function _setupCreditMemoTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn($setup->getTable('sales_creditmemo'), Fee::LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Landed Fee'
        ]);

        $setup->getConnection()->addColumn($setup->getTable('sales_creditmemo'), Fee::BASE_LANDED_FEE, [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length'   => '12,4',
            'default'  => '0.0000',
            'comment'  => 'Base Landed Fee'
        ]);
    }
}
