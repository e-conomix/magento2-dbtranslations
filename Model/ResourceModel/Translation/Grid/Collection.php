<?php
declare(strict_types=1);

namespace Economix\DbTranslations\Model\ResourceModel\Translation\Grid;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Translation\Model\ResourceModel\Translate as TranslateResource;

/**
 * UI listing collection for the DB translations grid.
 *
 * Exposes the DB column `locale` under the admin-safe alias `translation_locale`
 * so that inline-edit POSTs never carry a top-level `locale` parameter (which
 * would switch the adminhtml UI locale via the backend locale resolver).
 */
class Collection extends SearchResult implements SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface|null
     */
    protected $aggregations;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string|null $resourceModel
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable = 'translation',
        $resourceModel = TranslateResource::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->_map['fields']['key_id'] = 'main_table.key_id';
        $this->_map['fields']['translation_locale'] = 'main_table.locale';
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        // addExpressionFieldToSelect registers the alias in expressionFieldsToSelect
        // so it survives AbstractCollection::_initSelectFields() column rebuilds.
        $this->addExpressionFieldToSelect('translation_locale', 'main_table.locale', []);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Filter the collection by one or more store ids.
     *
     * @param int|int[]|null $storeId
     * @return $this
     */
    public function addStoreFilter($storeId): self
    {
        if ($storeId === null || $storeId === '') {
            return $this;
        }
        $storeIds = is_array($storeId) ? $storeId : [$storeId];
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
