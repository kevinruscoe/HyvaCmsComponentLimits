<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model;

use KevinRuscoe\HyvaCmsComponentLimits\Model\ResourceModel\Limit\CollectionFactory;
use Hyva\CmsLiveviewEditor\Model\Component\Provider as ComponentProvider;

/**
 * Restrict limitations: a component in one or more of them can only be placed where at least one
 * of those limitations' conditions match. Components in none of them can be placed anywhere.
 *
 * Allowlist limitations: where one or more of them match, only their components (and the children
 * those components accept) can be placed. Both kinds apply together, so either can disallow a component.
 *
 * Exclusive limitations are both a restrict and an allowlist limitation.
 */
class ComponentLimiter
{
    private CollectionFactory $collectionFactory;
    private PlacementContextProvider $placementContextProvider;
    private ComponentProvider $componentProvider;

    /** @var Limit[]|null */
    private ?array $limits = null;

    /** @var array<string, bool> limit id => conditions match, per placement */
    private array $matches = [];

    public function __construct(
        CollectionFactory $collectionFactory,
        PlacementContextProvider $placementContextProvider,
        ComponentProvider $componentProvider
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->placementContextProvider = $placementContextProvider;
        $this->componentProvider = $componentProvider;
    }

    /**
     * @param string[]|null $componentNames Only check these, or every component when null
     * @return string[]
     */
    public function getDisallowedComponents(string $type, int $entityId, ?array $componentNames = null): array
    {
        $limits = $this->getLimits();
        if (!$limits) {
            return [];
        }

        $context = $this->placementContextProvider->get($type, $entityId);
        $restrictionsByComponent = [];
        $allowlist = null;

        foreach ($limits as $limit) {
            if ($limit->restrictsComponents()) {
                foreach ($limit->getComponentNames() as $componentName) {
                    $restrictionsByComponent[$componentName][] = $limit;
                }
            }

            if ($limit->restrictsPlaces() && $this->matches($limit, $context)) {
                $allowlist = array_merge($allowlist ?? [], $limit->getComponentNames());
            }
        }

        $componentNames ??= array_keys($this->componentProvider->getComponents());
        $allowlist = $allowlist === null ? null : $this->withAcceptedChildren($allowlist);
        $disallowed = [];

        foreach ($componentNames as $componentName) {
            if ($allowlist !== null && !isset($allowlist[$componentName])) {
                $disallowed[] = $componentName;
                continue;
            }

            $restrictions = $restrictionsByComponent[$componentName] ?? [];
            if ($restrictions && !$this->anyMatch($restrictions, $context)) {
                $disallowed[] = $componentName;
            }
        }

        return $disallowed;
    }

    /**
     * Collect every component name used in Hyva CMS content JSON, at any depth
     *
     * @return string[]
     */
    public function collectComponentNames(string $json): array
    {
        $names = [];
        $this->walk(json_decode($json, true), $names);

        return array_keys($names);
    }

    private function walk($node, array &$names): void
    {
        if (!is_array($node)) {
            return;
        }

        if (isset($node['uid'], $node['component']) && is_string($node['component'])) {
            $names[$node['component']] = true;
        }

        foreach ($node as $child) {
            $this->walk($child, $names);
        }
    }

    /**
     * Allowing a parent like "usp_list" has to allow the items it accepts too, or it can't be filled
     *
     * @param string[] $componentNames
     * @return array<string, true>
     */
    private function withAcceptedChildren(array $componentNames): array
    {
        $components = $this->componentProvider->getComponents();
        $allowed = [];
        $queue = $componentNames;

        while ($queue) {
            $componentName = array_shift($queue);
            if (isset($allowed[$componentName])) {
                continue;
            }

            $allowed[$componentName] = true;
            $children = $components[$componentName]['children'] ?? null;
            if (is_array($children)) {
                array_push($queue, ...($children['config']['accepts'] ?? []));
            }
        }

        return $allowed;
    }

    /**
     * @param Limit[] $limits
     */
    private function anyMatch(array $limits, PlacementContext $context): bool
    {
        foreach ($limits as $limit) {
            if ($this->matches($limit, $context)) {
                return true;
            }
        }

        return false;
    }

    private function matches(Limit $limit, PlacementContext $context): bool
    {
        $key = $limit->getId() . '@' . $context->getData('content_type') . ':' . $context->getData('entity_id');

        return $this->matches[$key] ??= (bool) $limit->validate($context);
    }

    /**
     * @return Limit[]
     */
    private function getLimits(): array
    {
        if ($this->limits === null) {
            $this->limits = $this->collectionFactory->create()->addActiveFilter()->getItems();
        }

        return $this->limits;
    }
}
