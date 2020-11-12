<?php

namespace Nwogu\Bagpack\Traits;

trait InteractsWithFiles
{
    /**
     * Ensure a directory exists.
     * 
     * This method is used in case of
     * older laravel versions
     *
     * @param Illuminate\Filesystem\Filesysem $files
     * @param  string  $path
     * 
     * @return void
     */
    public function ensureDirectoryExists($files, $path)
    {
        if (! $files->isDirectory($path)) {
            $files->makeDirectory($path);
        }
    }
}