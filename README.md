# Hyvä CMS Component Limits

Control where [Hyvä CMS](https://www.hyva.io/hyva-commerce.html) components can be placed, from the Magento admin.

Out of the box, any Hyvä CMS component can be dropped into any page, block or menu. This module adds **Component Limitations**: rules built with Magento's conditions builder (the same one catalog and cart price rules use) that decide which components can go where.

## Features

- **Three modes** per limitation:
  - **Keep these components in these places**: the components can't be used anywhere else.
  - **Only allow these components in these places**: the places can't hold anything else.
  - **Lock these components and places together**: both.
- **Conditions** on editor type (CMS page, CMS block, menu, template, snippet, category/product attribute), CMS page (page, URL key, layout), CMS block (block, identifier) and menu (menu, identifier). No conditions means everywhere.
- **Hidden in the editor**: disallowed components are removed from the "Add a Component" picker, at any nesting depth.
- **Enforced on save**: content containing a disallowed component is rejected, whether it was added through the picker, pasted, imported, restored from version history or published on a schedule.
- **Children come along**: allowing a parent component (e.g. USP List) also allows the items it accepts.

If a component is in several limitations, it is allowed wherever any of them allow it.

## Requirements

- Magento 2.4 / PHP 8.1+
- Hyvä CMS (`hyva-themes/commerce-module-cms`) and Hyvä Menu Builder (`hyva-themes/commerce-module-menu-builder`)

## Installation

```bash
composer config repositories.hyva-cms-component-limits vcs https://github.com/kevinruscoe/HyvaCmsComponentLimits
composer require kevinruscoe/module-hyva-cms-component-limits
bin/magento module:enable KevinRuscoe_HyvaCmsComponentLimits
bin/magento setup:upgrade
```

## Usage

Go to **Stores > Configuration > Hyvä Commerce > Hyvä CMS Component Limitations** and click **Manage Component Limitations**.

1. Click **Add New Component Limitation**.
2. Pick a **Mode** (hover the "?" for examples) and the **Components** it applies to.
3. Under **Places**, add conditions, e.g. *CMS Block Identifier is `utility-bar`*.
4. Save, then reload any open Hyvä CMS editor tabs.

Access is controlled by the ACL resource **Stores > Settings > Configuration > Hyvä CMS Component Limitations**.

## How it works

- `Model/ComponentLimiter` works out which components are disallowed for the entity being edited.
- `view/adminhtml/templates/editor/limits.phtml` filters the editor's component picker. Hyvä caches the component list per browser rather than per entity, so the filtering happens client-side after the list loads.
- `Plugin/EnforceComponentLimits` checks every `Hyva\CmsLiveviewEditor\Model\Provider::saveContent()` call, and `Plugin/EditorComponentLimits` turns a rejected editor save into an error message in the editor.
