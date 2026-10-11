<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\ResourceModel\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Model\Limit;
use KevinRuscoe\HyvaCmsComponentLimits\Model\ResourceModel\Limit as LimitResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'rule_id';

    protected function _construct()
    {
        $this->_init(Limit::class, LimitResource::class);
    }

    public function addActiveFilter(): self
    {
        return $this->addFieldToFilter('is_active', 1);
    }
}
