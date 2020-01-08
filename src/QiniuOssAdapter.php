<?php


namespace Minz\Laravel\Qiniu\OSS;


use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

class QiniuOssAdapter extends AbstractAdapter
{
    const ACCESS_PUBLIC = "public";
    const ACCESS_PRIVATE = "private";

    protected $auth;
    protected $bucketManager;

    private $accessKey;
    private $accessSecret;
    private $bucket;
    private $domains;
    private $rootDir;
    private $ssl;
    private $public;

    public function __construct(string $accessKey, string $accessSecret, string $bucket, string $domains, string $rootDir, bool $ssl = false, bool $public = false)
    {
        $this->accessKey = $accessKey;
        $this->accessSecret = $accessSecret;
        $this->bucket = $bucket;
        $this->domains = $domains;
        $this->rootDir = $rootDir;
        $this->ssl = $ssl;
        $this->public = $public;

        $this->auth = new Auth($this->accessKey, $this->accessSecret);
    }

    /**
     * get bucket manager
     *
     * @return BucketManager
     */
    private function getBucketManager()
    {
        return $this->bucketManager ?: $this->bucketManager = new BucketManager($this->auth);
    }

    /**
     * @param string|null $path  bucket下文件夹绝对路径
     * @param int $expires  过期时间 s
     * @param array|null $policy 请参照七牛OSS文档
     * @param bool $strictPolicy
     * @return string
     */
    public function uploadToken(string $path = null, int $expires = 3600, array $policy = null, bool $strictPolicy = true)
    {
        $token = $this->auth->uploadToken($this->bucket, $path, $expires, $policy, $strictPolicy);
        return $token;
    }

    /**
     * 获取object public url
     *
     * @param $key
     * @return string
     */
    public function getUrl($key)
    {
        return "{$this->getHosts()}{$this->domains}/{$key}";
    }

    /**
     * get hosts
     *
     * @return string
     */
    private function getHosts()
    {
        return $this->ssl ? "https://" : "http://";
    }

    /**
     * 获取视频对象视频时长(s)
     *
     * @param $key
     * @return bool|int
     */
    public function videoDuration($key)
    {
        $baseUrl = $this->getUrl($key) . "?avinfo";
        $url = $this->auth->privateDownloadUrl($baseUrl);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        if ($response) {
            $videoObject = json_decode($response);
            return isset($videoObject->format->duration) ? intval($videoObject->format->duration) : false;
        }
        return false;
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {

    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {

    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {

    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {

    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {

    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {

    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {

    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {

    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {

    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {

    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        list($response, $err) = $this->getBucketManager()->stat($this->bucket, $path);
        return is_array($response) || !$err;
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        $baseUrl = $this->getUrl($path);
        if ($this->public == false) {
            $baseUrl = $this->auth->privateDownloadUrl($baseUrl, 7200);
        }
        $contents = file_get_contents($baseUrl);
        return compact('contents', 'path');
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {

    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {

    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        list($stats, $err) = $this->getBucketManager()->stat($this->bucket, $path);
        if ($err) return false;
        $stats['key'] = $path;
        return $this->formatFileInfo($stats);
    }

    /**
     * 格式化文件meta
     *
     * @param array $stats
     * @return array
     */
    private function formatFileInfo(array $stats)
    {
        return [
            "type" => "file",
            "path" => $stats["key"],
            "timestamp" => (int)floor($stats["putTime"] / 10000000),
            "size" => $stats['fsize'],
            "mimeType" => $stats['mimeType']
        ];
    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        $stats = $this->getMetadata($path);
        return isset($stats['size']) ? ['size' => $stats['size']] : false;
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        $stats = $this->getMetadata($path);
        $return = isset($stats['mimeType']) ? ["mimetype" => $stats['mimeType']] : false;
        return $return;
    }

    /**
     * Get the last modified time of a file as a timestamp.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {

    }

    /**
     * 下载文件到指定路径
     *
     * @param string $key
     * @param string|null $path
     * @param int $expires
     * @return string
     */
    public function download(string $key, string $path = null, int $expires = 3600)
    {
        $baseUrl = $this->privateDownloadUrl($key, $expires);
        $hostFileHandle = fopen($baseUrl, 'r');
        $path = $path ?? $key;
        $path = $this->rootDir . '/' . $path;

        $fh = fopen($path ?? $key, 'w');
        while (!feof($hostFileHandle)) {
            $output = fread($hostFileHandle, 10240);
            fwrite($fh, $output);
        }
        fclose($hostFileHandle);
        fclose($fh);
        return $path;
    }

    /**
     * get kodo private download url
     *
     * @param string $key
     * @param string|null $alias
     * @param int $expires
     * @return string
     */
    public function privateDownloadUrl(string $key, string $alias = null, int $expires = 3600)
    {
        $baseUrl = $this->getUrl($key);
        if ($alias) {
            $baseUrl .= "?attname=$alias";
        }
        if ($this->public == false) {
            $baseUrl = $this->auth->privateDownloadUrl($baseUrl, $expires);
        }
        return $baseUrl;
    }
}