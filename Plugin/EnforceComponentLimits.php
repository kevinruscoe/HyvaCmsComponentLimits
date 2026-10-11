<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Plugin;

use KevinRuscoe\HyvaCmsComponentLimits\Model\ComponentLimiter;
use KevinRuscoe\HyvaCmsComponentLimits\Model\PlacementContext;
use Hyva\CmsLiveviewEditor\Model\Provider;
use Magento\Framework\Exception\LocalizedException;

/**
 * Hard stop for every Hyva CMS save path: editor, version restore, scheduled publish and import
 */
class EnforceComponentLimits
{
    private ComponentLimiter $componentLimiter;

    public function __construct(ComponentLimiter $componentLimiter)
    {
        $this->componentLimiter = $componentLimiter;
    }

    public function beforeSaveContent(Provider $subject, string $type, int $entityId, string $componentsJson)
    {
        $disallowed = $this->componentLimiter->getDisallowedComponents(
            $type,
            $entityId,
            $this->componentLimiter->collectComponentNames($componentsJson)
        );

        if ($disallowed) {
            throw new LocalizedException(__(
                'The component(s) "%1" cannot be placed in this %2. See Content > Component Limitations.',
                implode('", "', $disallowed),
                __(PlacementContext::CONTENT_TYPES[$type] ?? $type)
            ));
        }

        return null;
    }
}
