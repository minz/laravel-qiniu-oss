<?php


namespace Minz\Laravel\Qiniu\OSS\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class Download extends AbstractPlugin
{
    /**
     * sign url.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'downloadFile';
    }

    /**
     * get key's url
     *
     * @param string $key
     * @param string|null $path
     * @param int $expires
     * @return mixed
     */
    public function handle(string $key, string $path = null, int $expires = 3600)
    {
        return $this->filesystem->getAdapter()->download($key, $path, $expires);
    }
}