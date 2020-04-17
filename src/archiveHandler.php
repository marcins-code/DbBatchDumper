<?php


trait archiveHandler
{
    /**
     * Usunięcie archiwuw starszych niz zadane
     * @param string $howOld
     */
    public function keepArchiveNotOlderThan(string $howOld)

    {
        $fileList = new \DirectoryIterator($this->targetDirectory);
        if ($fileList) {
            foreach ($fileList as $file) {

                if ($file->isDir() && preg_match('/dump/', $file->getFilename()) && ($file->getMTime() < (date(strtotime('-' . $howOld))))) {

                    self::removeAllFromDir($this->targetDirectory . '/' . $file);

                }
            }
        }
    }


    /**
     * Usuwanie wszystkich elementów z katalogu
     * @param $dir
     */
    private static function removeAllFromDir($dir)
    {
        $dirContent = glob($dir . '/*');
        foreach ($dirContent as $content) {
            if (is_file($content)) {
                unlink($content);
            }

            if (is_dir($content)) {
                self::removeAllFromDir($content);
            }
        }
        rmdir($dir);
    }

}
