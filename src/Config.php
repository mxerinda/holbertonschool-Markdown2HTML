<?php
namespace Markdown2Html;

use Markdown2Html\Theme\Theme;

class Config
{
    /**
     * Path to source folder containing markdown files
     *
     * @var string
     */
    public $src;

    /**
     * Path to destination folder (will be created if not exists)
     *
     * @var string
     */
    public $dest;

    /**
     * @var Theme
     */
    public $theme;
}
