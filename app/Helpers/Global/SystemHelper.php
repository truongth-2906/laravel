<?php

if (! function_exists('includeFilesInFolder')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     * @param array $skips
     */
    function includeFilesInFolder($folder, array $skips = [])
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php' && !in_array($it->current()->getFilename(), $skips)) {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (! function_exists('includeRouteFiles')) {

    /**
     * @param $folder
     * @param array $skips
     */
    function includeRouteFiles($folder, array $skips = [])
    {
        includeFilesInFolder($folder, $skips);
    }
}
