<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use KevinRuscoe\HyvaCmsComponentLimits\Model\LimitFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Limit implements HttpPostActionInterface
{
    private LimitFactory $limitFactory;

    public function __construct(Context $context, LimitFactory $limitFactory)
    {
        parent::__construct($context);
        $this->limitFactory = $limitFactory;
    }

    public function execute()
    {
        $limit = $this->limitFactory->create()->load((int) $this->getRequest()->getParam('id'));

        if ($limit->getId()) {
            try {
                $limit->delete();
                $this->messageManager->addSuccessMessage(__('The limitation has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('This limitation no longer exists.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
