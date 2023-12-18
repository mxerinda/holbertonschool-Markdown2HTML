<?php
namespace Markdown2Html;

use CallbackFilterIterator;
use DirectoryIterator;
use RuntimeException;
use SplFileInfo;

class Filesystem
{
    /**
     * @param string $dir
     *
     * @return SplFileInfo[]|\Iterator
     */
    public function getFilesOfDir($dir)
    {
        $iterator = new DirectoryIterator($dir);

        $iterator = new CallbackFilterIterator(
            $iterator,
            function (SplFileInfo $current, $key, DirectoryIterator $iterator) {
                return !$iterator->isDot();
            }
        );

        return $iterator;
    }

    /**
     * @param string $dir
     *
     * @throws RuntimeException
     */
    public function mkdir($dir)
    {
        if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException('could not create dir ' . $dir);
        }
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function readFile($path)
    {
        return file_get_contents($path);
    }

    /**
     * @param string $file
     * @param string $content
     *
     * @throws RuntimeException
     */
    public function writeFile($file, $content)
    {
        $this->ensureDir(dirname($file));

        file_put_contents($file, $content);
    }

    /**
     * Create directory if not exists.
     *
     * @param string $dir
     *
     * @throws RuntimeException
     */
    public function ensureDir($dir)
    {
        if (!is_dir($dir)) {
            $this->mkdir($dir);
        }
    }

    /**
     * Delete all files inside a directory.
     *
     * @param string $dir
     *
     * @return void
     */
    public function purge($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);

        if ($files !== false) {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $path = $dir . '/' . $file;

                if (is_dir($path)) {
                    $this->purge($path);
                    rmdir($path);
                } else {
                    unlink($path);
                }
            }
        }
    }

    /**
     * Copy file and create directory if not exists.
     *
     * @param string $src
     * @param string $dest
     *
     * @throws RuntimeException
     */
    public function copy($src, $dest)
    {
        if (is_dir($src)) {
            $this->ensureDir($dest);
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $this->copy($src . '/' . $file, $dest . '/' . $file);
                }
            }
        } else {
            $this->ensureDir(dirname($dest));

            copy($src, $dest);
        }
    }
}
