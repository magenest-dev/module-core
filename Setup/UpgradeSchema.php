<?php
/**
 * Created by PhpStorm.
 * User: ducquach
 * Date: 4/22/19
 * Time: 6:54 PM
 */
namespace Magenest\Core\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0')) {
            $this->addIsMagenestColumn($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addIsMagenestColumn($setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('adminnotification_inbox'),
            'is_magenest',
            [
                'nullable' => true,
                'type' => Table::TYPE_SMALLINT,
                'comment'  => 'Is Magenest',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('adminnotification_inbox'),
            'magenest_id',
            [
                'nullable' => true,
                'type' => Table::TYPE_INTEGER,
                'comment'  => 'Magenest notification ID',
            ]
        );
    }

}