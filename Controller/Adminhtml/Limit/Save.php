<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Controller\Adminhtml\Limit;
use KevinRuscoe\HyvaCmsComponentLimits\Model\Limit as LimitModel;
use KevinRuscoe\HyvaCmsComponentLimits\Model\LimitFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Limit implements HttpPostActionInterface
{
    private LimitFactory $limitFactory;

    public function __construct(Context $context, LimitFactory $limitFactory)
    {
        parent::__construct($context);
        $this->limitFactory = $limitFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int) ($data['rule_id'] ?? 0);
        $limit = $this->limitFactory->create();
        if ($id) {
            $limit->load($id);
            if (!$limit->getId()) {
                $this->messageManager->addErrorMessage(__('This limitation no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        // The conditions builder posts as rule[conditions][...]
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        unset($data['rule'], $data['rule_id'], $data['form_key']);

        try {
            $limit->loadPost($data);
            $limit->addData([
                'name' => $data['name'] ?? '',
                'is_active' => (int) ($data['is_active'] ?? 0),
                'mode' => in_array($data['mode'] ?? '', LimitModel::MODES, true)
                    ? $data['mode']
                    : LimitModel::MODE_RESTRICT,
                'components' => (array) ($data['components'] ?? []),
            ]);
            $limit->save();

            $this->messageManager->addSuccessMessage(__('The limitation has been saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $limit->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the limitation.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $id ?: null]);
    }
}
