<?php

namespace Minz\Laravel\Qiniu\OSS\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class BaseUrl extends AbstractPlugin
{
    /**
     * sign url.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getUrl';
    }

    /**
     * get key's url
     *
     * @param string $key
     * @return mixed
     */
    public function handle(string $key)
    {
        return $this->filesystem->getAdapter()->getUrl($key);
    }
}