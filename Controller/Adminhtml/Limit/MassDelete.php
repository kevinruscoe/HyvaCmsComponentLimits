<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use KevinRuscoe\HyvaCmsComponentLimits\Model\ResourceModel\Limit\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Limit implements HttpPostActionInterface
{
    private Filter $filter;
    private CollectionFactory $collectionFactory;

    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $size = $collection->getSize();

        foreach ($collection as $limit) {
            $limit->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 limitation(s) have been deleted.', $size));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
