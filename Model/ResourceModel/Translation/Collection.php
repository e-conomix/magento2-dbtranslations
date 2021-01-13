<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright   Copyright (c) 2017 E-CONOMIX GmbH (http://www.e-conomix.at)
 */

namespace Economix\DbTranslations\Model\ResourceModel\Translation;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * .
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{

    /**
     * @var string
     */
    protected $_idFieldName = 'key_id';
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            'translation',
            \Magento\Translation\Model\ResourceModel\Translate::class,
            'key_id',
            $connection
        );
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Economix\DbTranslations\Model\Translate::class,
            \Magento\Translation\Model\ResourceModel\Translate::class
        );
        $this->_map['fields']['key_id'] = 'main_table.key_id';
        $this->_map['fields']['store_id'] = 'store_table.store_id';
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Store) {
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $store = [$store];
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store_id', ['in' => $store], 'public');
        }
        return $this;
    }

    /**
     * @param $tableName
     * @param $linkField
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        foreach($this as $item) {
            $item->setStores([(int)$item->getStoreId()]);
        }

        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['ecx_catalog_brand_store' => $this->getTable($tableName)])
                ->where('ecx_catalog_brand_store.' . $linkField . ' IN (?)', $linkedIds);
            // @codingStandardsIgnoreStart
            $result = $connection->fetchAll($select);
            // @codingStandardsIgnoreEnd
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(
                        \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        $storesData[$linkedId],
                        true
                    );
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }
}
