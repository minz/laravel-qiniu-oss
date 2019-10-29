<?php


namespace Minz\Laravel\Qiniu\OSS;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;


class QiniuOssServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        Storage::extend("qiniu", function ($app, $config) {
            $adpter = new QiniuOssAdapter(
                $config['access_key'],
                $config['access_secret'],
                $config['bucket']
            );
            $fileSystem = new Filesystem($adpter);
            $fileSystem->addPlugin(new UploadToken());

            return$fileSystem;
        });
    }
}