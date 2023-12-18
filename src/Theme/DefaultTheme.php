<?php
namespace Markdown2Html\Theme;

class DefaultTheme extends Theme
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $additionalCss;

    /**
     * @var string[]
     */
    public $naviLinks = [];

    /**
     * @var string
     */
    private $themePath;

    /**
     * @param string|null $themePath
     */
    public function __construct($themePath = null)
    {
        $this->themePath = $themePath ?: __DIR__ . '/../../templates/default';
        parent::__construct($this->themePath . '/doc.php');
    }

    /**
     * @return array
     */
    public function getCopyFiles()
    {
        return [
            $this->themePath . '/assets' => 'assets',
        ];
    }
}
