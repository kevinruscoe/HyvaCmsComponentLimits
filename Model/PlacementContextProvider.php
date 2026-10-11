<?php

namespace KevinRuscoe\HyvaCmsComponentLimits\Model;

use Hyva\MenuBuilder\Api\MenuRepositoryInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class PlacementContextProvider
{
    private PlacementContextFactory $placementContextFactory;
    private PageRepositoryInterface $pageRepository;
    private BlockRepositoryInterface $blockRepository;
    private MenuRepositoryInterface $menuRepository;

    /** @var PlacementContext[] */
    private array $contexts = [];

    public function __construct(
        PlacementContextFactory $placementContextFactory,
        PageRepositoryInterface $pageRepository,
        BlockRepositoryInterface $blockRepository,
        MenuRepositoryInterface $menuRepository
    ) {
        $this->placementContextFactory = $placementContextFactory;
        $this->pageRepository = $pageRepository;
        $this->blockRepository = $blockRepository;
        $this->menuRepository = $menuRepository;
    }

    public function get(string $type, int $entityId): PlacementContext
    {
        $key = $type . ':' . $entityId;
        if (!isset($this->contexts[$key])) {
            $data = array_fill_keys(PlacementContext::ATTRIBUTES, null);
            $data['content_type'] = $type;
            $data['entity_id'] = $entityId;

            try {
                $data = array_merge($data, $this->loadEntityData($type, $entityId));
            } catch (NoSuchEntityException $e) {
                // New or deleted entity: only the content type can match
            }

            $this->contexts[$key] = $this->placementContextFactory->create(['data' => $data]);
        }

        return $this->contexts[$key];
    }

    private function loadEntityData(string $type, int $entityId): array
    {
        if (!$entityId) {
            return [];
        }

        switch ($type) {
            case 'cms_page':
                $page = $this->pageRepository->getById($entityId);
                return [
                    'page_id' => $page->getId(),
                    'page_identifier' => $page->getIdentifier(),
                    'page_layout' => $page->getPageLayout(),
                ];
            case 'cms_block':
                $block = $this->blockRepository->getById($entityId);
                return [
                    'block_id' => $block->getId(),
                    'block_identifier' => $block->getIdentifier(),
                ];
            case 'menu':
                $menu = $this->menuRepository->get($entityId);
                return [
                    'menu_id' => $menu->getId(),
                    'menu_identifier' => $menu->getIdentifier(),
                ];
        }

        return [];
    }
}
