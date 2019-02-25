<?php
namespace Economix\DbTranslations\Block\Adminhtml\Brand\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Generic
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Economix\DbTranslations\Model\TranslateFactory
     */
    private $translateFactory;

    /**
     * @param Context $context
     * @param \Economix\DbTranslations\Model\TranslateFactory $translateFactory
     */
    public function __construct(
        Context $context,
        \Economix\DbTranslations\Model\TranslateFactory $translateFactory
    ) {
        $this->context = $context;
        $this->translateFactory = $translateFactory;
    }

    /**
     * Return translation  ID
     *
     * @return int|null
     */
    public function getTranslationId()
    {
        try {
            return $this->translateFactory->create()->load(
                $this->context->getRequest()->getParam('key_id')
            )->getKeyId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
