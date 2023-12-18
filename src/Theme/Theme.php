<?php
namespace Markdown2Html\Theme;

use Markdown2Html\TreeItem;

abstract class Theme
{
    /**
     * @var string
     */
    protected $templateFile;

    /**
     * @var TreeItem[]
     */
    protected $treeItems;

    /**
     * @var string
     */
    protected $currentItemId;

    /**
     * @var string
     */
    protected $currentContent;

    /**
     * @var NavItem[]
     */
    protected $navItems = [];

    /**
     * @param string $templateFile
     */
    public function __construct($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @return array
     */
    abstract public function getCopyFiles();

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @param TreeItem[] $treeItems
     */
    public function setTreeItems(array $treeItems)
    {
        $this->treeItems = $treeItems;
    }

    /**
     * @param string $id
     * @param string $docContent
     */
    public function setCurrentItem($id, $docContent)
    {
        $this->currentItemId  = $id;
        $this->currentContent = $docContent;
        $this->navItems       = $this->createNavItems($this->treeItems);
    }

    /**
     * @return string
     */
    public function getCurrentItemId()
    {
        return $this->currentItemId;
    }

    /**
     * @return string
     */
    public function getCurrentContent()
    {
        return $this->currentContent;
    }

    /**
     * @return NavItem[]
     */
    public function getNavItems()
    {
        return $this->navItems;
    }

    /**
     * @return NavItem|null
     */
    public function getCurrentNavItem()
    {
        foreach ($this->navItems as $item) {
            if ($item->id === $this->currentItemId) {
                return $item;
            }

            $child = $item->getChildById($this->currentItemId);
            if ($child !== null) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @return NavItem|null
     */
    public function getPreviousNavItem()
    {
        $current = $this->getCurrentNavItem();
        if (!$current) {
            return null;
        }

        return $this->getPreviousNavItemOf($current);
    }

    /**
     * @return NavItem|null
     */
    public function getNextNavItem()
    {
        $current = $this->getCurrentNavItem();
        if (!$current) {
            return null;
        }

        if ($current->children) {
            return reset($current->children);
        }

        $next = $this->getNextNavItemOf($current);

        if (!$next && $current->parent) {
            $next = $this->getNextNavItemOf($current->parent);
        }

        return $next;
    }

    /**
     * @param TreeItem[] $treeItems
     * @param NavItem    $parent
     *
     * @return NavItem[]
     */
    protected function createNavItems(array $treeItems, NavItem $parent = null)
    {
        /** @var NavItem[] $navItems */
        $navItems = [];

        foreach ($treeItems as $treeItem) {
            if ($treeItem->isAsset) {
                continue;
            }

            $nav = $this->createNavItem($treeItem, $parent);
            if ($nav === null) {
                continue;
            }

            if (!isset($navItems[$nav->relUrl]) || !$navItems[$nav->relUrl]->children) {
                $navItems[$nav->relUrl] = $nav;
            }
        }

        return array_values($navItems);
    }

    /**
     * @param TreeItem $treeItem
     * @param NavItem  $parent
     *
     * @return NavItem
     */
    protected function createNavItem(TreeItem $treeItem, NavItem $parent = null)
    {
        // don't create navi entry for index page
        if ($parent === null && basename($treeItem->src) === 'index.md') {
            return null;
        }

        $nav         = new NavItem();
        $nav->id     = $treeItem->getId();
        $nav->label  = $treeItem->label;
        $nav->relUrl = $treeItem->relUrl;

        $nav->parent = $parent;

        if ($treeItem->isDir) {
            $nav->relUrl .= '.html';
            $nav->children = $this->createNavItems($treeItem->children, $nav);
            if (!$nav->children) {
                return null;
            }
        }

        return $nav;
    }

    /**
     * @param NavItem $current
     *
     * @return NavItem|null
     */
    protected function getPreviousNavItemOf(NavItem $current)
    {
        $siblings = $current->parent ? $current->parent->children : $this->navItems;
        $last     = $current->parent;

        foreach ($siblings as $sibling) {
            if ($sibling->id === $current->id) {
                return $last;
            }

            $last = $sibling;
        }

        return null;
    }

    /**
     * @param NavItem $current
     *
     * @return NavItem|null
     */
    public function getNextNavItemOf(NavItem $current)
    {
        $siblings = $current->parent ? $current->parent->children : $this->navItems;
        $found    = false;

        foreach ($siblings as $sibling) {
            if ($found) {
                return $sibling;
            }

            if ($sibling->id === $current->id) {
                $found = true;
            }
        }

        return null;
    }
}
