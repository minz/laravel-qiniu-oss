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
     * @param string|null $alias
     * @param int $expire
     * @return mixed
     */
    public function handle(string $key, string $alias = null, int $expire = 3600)
    {
        return $this->filesystem->getAdapter()->privateDownloadUrl($key, $alias, $expire);
    }
}