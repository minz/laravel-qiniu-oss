<?php


namespace Minz\Laravel\Qiniu\OSS\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class UploadToken extends AbstractPlugin
{
    /**
     * sign url.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'uploadToken';
    }

    /**
     * handle.
     *
     * @param string $prefix
     * @param int $expire
     * @param array|null $policy
     * @param bool $strictPolicy
     * @return mixed
     */
    public function handle($prefix = '',  $expire = 30, array $policy = null, bool $strictPolicy = true)
    {
        return $this->filesystem->getAdapter()->uploadToken($prefix, $expire,  $policy, $strictPolicy);
    }
}