<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Limit extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('kevinruscoe_hyva_cms_component_limit', 'rule_id');
    }
}
