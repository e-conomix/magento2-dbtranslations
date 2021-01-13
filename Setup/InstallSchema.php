<?php
namespace Economix\DbTranslations\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema .
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if ($installer->tableExists('translation')
            && !in_array(
                $setup->getIdxName('translation', ['string', 'translate'], AdapterInterface::INDEX_TYPE_FULLTEXT),
                $installer->getConnection()->getIndexList('translation')
            )
        ) {
            $installer->getConnection()->addIndex(
                $installer->getTable('translation'),
                $setup->getIdxName(
                    $installer->getTable('translation'),
                    ['string','translate'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'string',
                    'translate',
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    }
}
