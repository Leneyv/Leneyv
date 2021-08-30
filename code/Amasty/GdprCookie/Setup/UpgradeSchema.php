<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $this->addCookieLifetime($setup);
        }

        $setup->endSetup();
    }

    protected function addCookieLifetime(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(\Amasty\GdprCookie\Setup\Operation\CreateCookieTable::TABLE_NAME);
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'cookie_lifetime',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => false,
                'default'  => '',
                'comment'  => 'Cookie Lifetime'
            ]
        );

        $connection->dropColumn($table, 'consent_type');
    }
}
