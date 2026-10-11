<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Block\Adminhtml\Limit\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        if (!$this->getId()) {
            return [];
        }

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => sprintf(
                "deleteConfirm('%s', '%s', {data: {}})",
                __('Are you sure you want to delete this limitation?'),
                $this->getUrl('*/*/delete', ['id' => $this->getId()])
            ),
            'sort_order' => 20,
        ];
    }
}
