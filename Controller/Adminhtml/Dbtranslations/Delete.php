<?php

namespace Economix\DbTranslations\Controller\Adminhtml\Dbtranslations;

use Economix\DbTranslations\Model\Translate;
use Economix\DbTranslations\Model\TranslateFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Economix\DbTranslations\Model\TranslateFactory
     */
    private $translationFactory;

    public function __construct(
        TranslateFactory $translationFactory,
        Action\Context $context
    )
    {
        parent::__construct($context);
        $this->translationFactory = $translationFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('key_id');
        if ($id) {
            try {
                /** @var Translate $translate */
                $translate = $this->translationFactory->create();
                $translate->load($id)->delete();
                $this->messageManager->addSuccessMessage(__('The translation has been deleted.'));
                $resultRedirect->setPath('ecx_dbtranslations/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The translation no longer exists.'));
                return $resultRedirect->setPath('ecx_dbtranslations/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('ecx_dbtranslations/dbtranslation/edit', ['key_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the translation'));
                return $resultRedirect->setPath('ecx_dbtranslations/dbtranslation/edit', ['key_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a translation to delete.'));
        $resultRedirect->setPath('ecx_dbtranslations/*/');
        return $resultRedirect;
    }
}
