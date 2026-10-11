<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use KevinRuscoe\HyvaCmsComponentLimits\Model\LimitFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Edit extends Limit implements HttpGetActionInterface
{
    private LimitFactory $limitFactory;
    private Registry $registry;

    public function __construct(Context $context, LimitFactory $limitFactory, Registry $registry)
    {
        parent::__construct($context);
        $this->limitFactory = $limitFactory;
        $this->registry = $registry;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $limit = $this->limitFactory->create();

        if ($id) {
            $limit->load($id);
            if (!$limit->getId()) {
                $this->messageManager->addErrorMessage(__('This limitation no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $this->registry->register('current_component_limit', $limit);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Config::system_config');
        $resultPage->getConfig()->getTitle()->prepend(
            $limit->getId() ? $limit->getName() : __('New Component Limitation')
        );

        return $resultPage;
    }
}
