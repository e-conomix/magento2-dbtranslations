<?php
namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;

use Economix\DbTranslations\Api\TranslateInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Economix\DbTranslations\Model\TranslateFactory
     */
    private $translateFactory;

    /**
     * @param \Economix\DbTranslations\Model\TranslateFactory $translateFactory
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Economix\DbTranslations\Model\TranslateFactory $translateFactory,
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    )
    {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context);
        $this->translateFactory = $translateFactory;
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $translation = null;
        $data = $this->getRequest()->getPostValue();
        $id = !empty($data['key_id']) ? $data['key_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $translation = $this->translateFactory->create()->load($id);
            } else {
                unset($data['key_id']);
                $translation = $this->translateFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($translation, $data, TranslateInterface::class);
            $translation->save();
            $this->messageManager->addSuccessMessage(__('You saved the Translation'));
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('ecx_dbtranslations/dbtranslations/edit', ['key_id' => $translation->getId()]);
            } else {
                $resultRedirect->setPath('ecx_dbtranslations/dbtranslations');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($translation != null) {
                $this->storeTranslationDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $translation,
                        TranslateInterface::class
                    )
                );
            }
            $resultRedirect->setPath('ecx_dbtranslations/dbtranslations/edit', ['key_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the translation'));
            if ($translation != null) {
                $this->storeTranslationDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $translation,
                        TranslateInterface::class
                    )
                );
            }
            $resultRedirect->setPath('ecx_dbtanslations/dbtranslations/edit', ['key_id' => $id]);
        }
        return $resultRedirect;
    }


    /**
     * @param $translationData
     */
    protected function storeTranslationDataToSession($translationData)
    {
        $this->_getSession()->setEcxDbTranslationsTranslationData($translationData);
    }
}
