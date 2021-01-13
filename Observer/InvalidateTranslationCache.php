<?php
/**
 * @author      Benjamin Rosenberger <rosenberger@e-conomix.at>
 * @copyright   Copyright (c) 2017 E-CONOMIX GmbH (http://www.e-conomix.at)
 */

namespace Economix\DbTranslations\Observer;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class InvalidateTranslationCache .
 */
class InvalidateTranslationCache implements ObserverInterface
{
    /**
     * @var TypeListInterface
     */
    private $typeList;

    /**
     * InvalidateTranslationCache constructor.
     * @param TypeListInterface $typeList
     */
    public function __construct(TypeListInterface $typeList)
    {
        $this->typeList = $typeList;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->typeList->invalidate('translate');
    }
}
