<?php
namespace Markdown2Html;

class TreeItem
{
    /**
     * @var string
     */
    public $src;

    /**
     * @var string
     */
    public $dest;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $relUrl;

    /**
     * @var int
     */
    public $sort = 0;

    /**
     * @var bool
     */
    public $isAsset = false;

    /**
     * @var bool
     */
    public $isDir = false;

    /**
     * @var TreeItem[]
     */
    public $children = [];

    /**
     * @var TreeItem|null
     */
    public $parent;

    /**
     * @return string
     */
    public function getId()
    {
        $val = $this->src;
        if (preg_match('/^(.*)\.md$/', $val, $matches)) {
            $val = $matches[1];
        }

        return md5($val);
    }
}
