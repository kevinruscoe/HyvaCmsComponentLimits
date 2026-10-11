<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Plugin;

use KevinRuscoe\HyvaCmsComponentLimits\Model\ComponentLimiter;
use KevinRuscoe\HyvaCmsComponentLimits\Model\PlacementContext;
use Hyva\CmsLiveviewEditor\Magewire\LiveviewComposer;

/**
 * Rejects the editor save with a flash message before it reaches Provider::saveContent(),
 * where EnforceComponentLimits would otherwise throw an unhandled exception
 */
class EditorComponentLimits
{
    private ComponentLimiter $componentLimiter;

    public function __construct(ComponentLimiter $componentLimiter)
    {
        $this->componentLimiter = $componentLimiter;
    }

    public function afterComponentsToValidJson(LiveviewComposer $subject, ?string $result): ?string
    {
        if ($result === null) {
            return null;
        }

        $disallowed = $this->componentLimiter->getDisallowedComponents(
            $subject->contentType,
            (int) $subject->contentId,
            $this->componentLimiter->collectComponentNames($result)
        );

        if (!$disallowed) {
            return $result;
        }

        $subject->dispatchErrorMessage(__(
            'The component(s) "%1" cannot be placed in this %2. Remove them and save again.',
            implode('", "', $disallowed),
            __(PlacementContext::CONTENT_TYPES[$subject->contentType] ?? $subject->contentType)
        ));

        // doSave() records the content as saved before calling us; clear that so the next attempt isn't "No changes"
        $subject->lastSavedDraftContentJson = '';
        $subject->lastSavedPublishedContentJson = '';

        return null;
    }
}
