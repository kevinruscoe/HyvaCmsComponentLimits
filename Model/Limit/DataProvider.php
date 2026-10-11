<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model\Limit;

use KevinRuscoe\HyvaCmsComponentLimits\Model\ResourceModel\Limit\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private ?array $loadedData = null;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        foreach ($this->collection->getItems() as $limit) {
            $data = $limit->getData();
            $data['components'] = $limit->getComponentNames();
            $this->loadedData[$limit->getId()] = $data;
        }

        return $this->loadedData;
    }
}
