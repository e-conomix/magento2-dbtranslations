<?php

namespace Economix\DbTranslations\Ui\DataProvider\Translation\Form\Modifier;

use Economix\DbTranslations\Model\Translate;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class TranslationData implements ModifierInterface
{
    /**
     * @var \Economix\DbTranslations\Model\ResourceModel\Translation\Collection
     */
    protected $collection;

    /**
     * @param \Economix\DbTranslations\Model\ResourceModel\Translation\CollectionFactory $translationCollectionFactory
     */
    public function __construct(
        \Economix\DbTranslations\Model\ResourceModel\Translation\CollectionFactory $translationCollectionFactory
    ) {
        $this->collection = $translationCollectionFactory->create();
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $items = $this->collection->getItems();
        /** @var $translation Translate */
        foreach ($items as $translation) {
            $_data = $translation->getData();
            if (isset($_data['store_id'])) {
                $_data['stores'] = [$_data['store_id']];
            }
            $translation->setData($_data);
            $data[$translation->getId()] = $_data;
        }
        return $data;
    }
}
