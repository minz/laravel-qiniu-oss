<?php


namespace Minz\Laravel\Qiniu\OSS\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class GetDownloadUrl extends AbstractPlugin
{
    /**
     * sign url.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getDownloadUrl';
    }

    /**
     * get key download url with token.
     *
     * @param string $key
     * @param int $expire
     * @return string $downloadUrl
     */
    public function handle(string $key, int $expire = 3600)
    {
        return $this->filesystem->getAdapter()->privateDownloadUrl($key, $expire);
    }
}