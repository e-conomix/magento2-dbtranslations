<?php
namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;

use \Economix\DbTranslations\Model\TranslateFactory;
use \Economix\DbTranslations\Controller\RegistryConstants;
use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Economix\DbTranslations\Model\TranslateFactory
     */
    private $translationFactory;

    /**
     * Edit constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        TranslateFactory $translationFactory,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->translationFactory = $translationFactory;
    }

    /**
     * Initialize current translation and set it in the registry.
     *
     * @return int
     */
    protected function _initTranslation()
    {
        $translationId = $this->getRequest()->getParam('key_id');
        $this->registry->register(RegistryConstants::CURRENT_TRANSLATION_ID, $translationId);

        return $translationId;
    }

    /**
     * Edit or create author
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $translationId = $this->_initTranslation();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Economix_DbTranslations::db_translations');
        $resultPage->getConfig()->getTitle()->prepend(__('Translations'));
        $resultPage->addBreadcrumb(__('Content'), __('Content'));
        $resultPage->addBreadcrumb(__('DB Translations'), __('DB Translations'), $this->getUrl('ecx_dbtranslations/dbtranslations'));

        if ($translationId === null) {
            $resultPage->addBreadcrumb(__('New Translation'), __('New Translation'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Translation'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Translation'), __('Edit Translation'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->translationFactory->create()->load($translationId)->getString()
            );
        }
        return $resultPage;
    }
}
