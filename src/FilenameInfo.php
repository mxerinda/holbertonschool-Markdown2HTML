<?php
namespace Markdown2Html;

class FilenameInfo
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var int
     */
    private $sort = 0;

    /**
     * @var string
     */
    private $label;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;

        if (preg_match('/^(\d+)\.(.*)$/', $filename, $matches) > 0) {
            $this->sort     = (int)$matches[1];
            $this->filename = $matches[2];
        }

        $this->label = $this->convertToLabel($this->filename);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function convertToLabel($value)
    {
        $value = preg_replace('/(?<!-)-(?!-)/U', ' ', $value);
        $value = preg_replace('/(?<!-)--(?!-)/U', '-', $value);
        $value = preg_replace('/(?<!-)---(?!-)/U', ' - ', $value);

        return $value;
    }
}
