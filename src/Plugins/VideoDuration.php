<?php


namespace Minz\Laravel\Qiniu\OSS\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class VideoDuration extends AbstractPlugin
{
    /**
     * sign url.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'videoDuration';
    }

    /**
     * 获取视频类型object duration
     *
     * @param $key
     * @return mixed
     */
    public function handle($key)
    {
        return $this->filesystem->getAdapter()->videoDuration($key);
    }
}