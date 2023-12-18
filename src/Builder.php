<?php
namespace Markdown2Html;

use Markdown2Html\Theme\Theme;
use Parsedown;
use SplFileInfo;

class Builder
{
    /**
     * @var Parsedown
     */
    private $parsedown;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Parsedown  $parsedown
     * @param Filesystem $filesystem
     */
    public function __construct(Parsedown $parsedown = null, Filesystem $filesystem = null)
    {
        $this->parsedown  = $parsedown ?: new ExtendedParsedown();
        $this->filesystem = $filesystem ?: new Filesystem();
    }

    /**
     * @param string $src
     * @param string $dest
     * @param Theme  $theme
     *
     * @throws \RuntimeException
     */
    public function build($src, $dest, Theme $theme)
    {
        $this->filesystem->purge($dest);

        foreach ($theme->getCopyFiles() as $from => $to) {
            $this->filesystem->copy($from, $dest . '/' . $to);
        }

        $treeItems = $this->parse(new SplFileInfo($src), $dest);
        $theme->setTreeItems($treeItems);

        //build all doc pages
        foreach ($treeItems as $item) {
            $this->exportFile($item, $theme);
        }

        //build index page
        if (!$this->filesystem->exists($dest . '/index.html')) {
            $theme->setCurrentItem('index', '');
            $html = $this->renderTemplate($theme);
            $this->filesystem->writeFile($dest . '/index.html', $html);
        }
    }

    /**
     * @param SplFileInfo $src
     * @param string      $destDir
     * @param TreeItem    $parent
     *
     * @return TreeItem[]
     */
    private function parse(SplFileInfo $src, $destDir, TreeItem $parent = null)
    {
        $files = $this->filesystem->getFilesOfDir($src->getPathname());
        $items = [];

        foreach ($files as $file) {
            $item = new TreeItem();

            if ($file->isDir()) {
                $item->isDir  = true;
                $filenameInfo = new FilenameInfo($file->getBasename());
                $item->sort   = $filenameInfo->getSort();
                $item->label  = $filenameInfo->getLabel();
                $destFilename = $filenameInfo->getFilename();
            } elseif (in_array($file->getExtension(), ['md', 'markdown'], true)) {
                $filenameInfo = new FilenameInfo($file->getBasename('.' . $file->getExtension()));
                $item->sort   = $filenameInfo->getSort();
                $item->label  = $filenameInfo->getLabel();
                $destFilename = $filenameInfo->getFilename() . '.html';
            } else {
                $item->isAsset = true;
                $destFilename  = $file->getBasename();
            }

            $item->relUrl = ($parent ? $parent->relUrl . '/' : '') . $destFilename;
            $item->src    = $file->getPathname();
            $item->dest   = $destDir . '/' . $destFilename;
            $item->parent = $parent;

            if ($file->isDir()) {
                $item->children = $this->parse($file, $item->dest, $item);
            }

            $items[] = $item;
        }

        usort($items, function (TreeItem $a, TreeItem $b) {
            if ($a->sort === $b->sort) {
                return 0;
            }

            return $a->sort > $b->sort ? 1 : -1;
        });

        return $items;
    }

    /**
     * @param TreeItem $item
     * @param Theme    $theme
     *
     * @throws \RuntimeException
     */
    private function exportFile(TreeItem $item, Theme $theme)
    {
        if ($item->isAsset) {
            $this->filesystem->copy($item->src, $item->dest);

            return;
        }

        if ($item->isDir) {
            foreach ($item->children as $child) {
                $this->exportFile($child, $theme);
            }

            $dest = $item->dest . '.html';

            // don't create index if there is a custom doc file
            if ($this->filesystem->exists($dest)) {
                return;
            }

            // write index.html for folders
            $theme->setCurrentItem($item->getId(), '');

            // folders for assets don't need index.html
            if (!$theme->getCurrentNavItem()) {
                return;
            }

            $html = $this->renderTemplate($theme);
            $this->filesystem->writeFile($dest, $html);

            return;
        }

        $content = $this->filesystem->readFile($item->src);

        $html = $this->parsedown->text($content);
        $theme->setCurrentItem($item->getId(), $html);

        $html = $this->renderTemplate($theme);

        $this->filesystem->writeFile($item->dest, $html);
    }

    /**
     * @param Theme $theme
     *
     * @return string
     */
    private function renderTemplate(Theme $theme)
    {
        ob_start();

        require $theme->getTemplateFile();

        return ob_get_clean();
    }
}
