<?php
namespace Markdown2Html\Theme;

class NavItem
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $relUrl;

    /**
     * @var NavItem[]
     */
    public $children = [];

    /**
     * @var NavItem|null
     */
    public $parent;

    /**
     * @return int
     */
    public function getDepth()
    {
        if ($this->parent) {
            return $this->parent->getDepth() + 1;
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getRelBaseUrl()
    {
        return str_repeat('../', $this->getDepth());
    }

    /**
     * @return NavItem[]
     */
    public function getBreadcrumb()
    {
        $breadcrumb = [];

        if ($this->parent) {
            $breadcrumb = $this->parent->getBreadcrumb();
        }

        $breadcrumb[] = $this;

        return $breadcrumb;
    }

    /**
     * @param string $id
     *
     * @return NavItem|null
     */
    public function getChildById($id)
    {
        foreach ($this->children as $child) {
            if ($child->id === $id) {
                return $child;
            }

            $subChild = $child->getChildById($id);
            if ($subChild !== null) {
                return $subChild;
            }
        }

        return null;
    }
}
