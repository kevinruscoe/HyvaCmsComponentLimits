<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Block\Adminhtml\Limit\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class GenericButton
{
    protected UrlInterface $urlBuilder;
    protected Registry $registry;

    public function __construct(Context $context, Registry $registry)
    {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    public function getId(): ?int
    {
        $limit = $this->registry->registry('current_component_limit');

        return $limit && $limit->getId() ? (int) $limit->getId() : null;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
