<h1 align="center">laravel qiniu cloud oss</h1>

<p align="center">
<a href="https://www.qiniu.com/products/kodo">qiniu</a> storage for Laravel based on <a href="https://github.com/qiniu/php-sdk">qiniu/php-sdk</a>.
</p>



## Requirement

-   PHP >= 7.0

## Installing

```shell
$ composer require "minz/laravel-qiniu-oss" -vvv
```

## Configuration

1. After installing the library, register the `\Minz\Laravel\Qiniu\OSS\QiniuOssServiceProvider::class` in your `config/app.php` file:

```php
'providers' => [
    ......
    \Minz\Laravel\Qiniu\OSS\QiniuOssServiceProvider::class,
],
```

> Laravel 5.5+ skip

2. Add a new disk to your `config/filesystems.php` config:

```php
<?php

return [
   'disks' => [
        //...
        "qiniu" => [
            'driver' => "qiniu",
            'access_key' => env('QINIU_ACCESS_KEY'),
            'access_secret' => env('QINIU_SECRET_KEY'),
            'bucket' => env('QINIU_BUCKET'),
            'domain' => env('QINIU_DOMAIN'),
            'ssl' => false,  //是否使用ssl协议
        ]
    ]
];
```

## Usage
```php
$disk = Storage::disk('qiniu');

#是否存在key
$disk->has($key);

#获取key metadata
$disk->getMetadata($key);

#获取key size
$disk->getSize($key);

#获取mimeType
$disk->getMimetype($key);

#extension
#获取视频类object时长
$disk->videoDuration($key)

#获取object baseUrl
$disk->getUrl($key);

#获取上传token
$disk->uploadToken("dir1/dir2/demo.mp3")
```

## 前端 web 直传配置

oss 直传有三种方式，当前扩展包使用的是最完整的 [服务端签名直传并设置上传回调](https://developer.qiniu.com/kodo/sdk/1241/php) 方式，扩展包只生成前端页面上传所需的签名参数，前端上传实现可参考 [官方文档中的实例](https://help.aliyun.com/document_detail/31927.html?spm=a2c4g.11186623.2.10.5602668eApjlz3#concept-qp2-g4y-5db) 或自行搜索

```php
$disk = Storage::disk('qiniu');
/**
 * @param string|null $path 当前存储object的绝对路径，包含当前object name, eg: dir1/dir2/demo.mp3
 * @param int $expires  过期时间 s
 * @param array|null $policy 请参照七牛OSS文档
 * @param bool $strictPolicy
 * @return string
 */
$config = $disk->uploadToken($path, $expire, $policy, $strictPolicy);
```

## depend

-   [qiniu/php-sdk](https://github.com/qiniu/php-sdk">qiniu/php-sdk)
-   [league/flsystem](https://github.com/thephpleague/flysystem)
## License

MIT
