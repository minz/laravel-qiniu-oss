<?php


namespace Minz\Laravel\Qiniu\OSS;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Minz\Laravel\Qiniu\OSS\Plugins\BaseUrl;
use Minz\Laravel\Qiniu\OSS\Plugins\Download;
use Minz\Laravel\Qiniu\OSS\Plugins\GetDownloadUrl;
use Minz\Laravel\Qiniu\OSS\Plugins\UploadToken;
use Minz\Laravel\Qiniu\OSS\Plugins\VideoDuration;


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
                $config['bucket'],
                $config['domain'],
                $config['root'],
                $config['ssl'],
                $config['public']
            );
            $fileSystem = new Filesystem($adpter);
            $fileSystem->addPlugin(new UploadToken());
            $fileSystem->addPlugin(new VideoDuration());
            $fileSystem->addPlugin(new BaseUrl());
            $fileSystem->addPlugin(new Download());
            $fileSystem->addPlugin(new GetDownloadUrl());

            return$fileSystem;
        });
    }
}