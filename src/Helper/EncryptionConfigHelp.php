<?php
/**
 * 配置文件读取辅助类
 * User: zhaoguanglai
 * Date: 2018/10/30
 * Time: 下午1:40
 */

namespace HxsdHelp\Help;

use Exception;
class EncryptionConfigHelp
{
    const APPLICATION_PATH = '';
    private $configPath;
    //临时config目录
    private $tempPathConfig = [];
    private static $config = [];
    private static $instance = null;
    //文件扩展
    private $ext = 'php';

    public function __construct()
    {
        $this->configPath = env('ENCRYPTION_PATH');
    }

    /**
     * 获取key相对应的配置项
     *
     * @param $key //api.api.
     * @return mixed
     * @throws Exception
     */
    public function get($key)
    {
        $ext = $this->ext;
        if(!isset(static::$config[$this->ext][$key])){
            if($this->getPath($key) == true){
                //回溯配置文件目录
                if ($ext == 'php') {
                    static::$config[$ext][$key] = $this->getPhpConfig();
                }
            }else{
                throw new Exception('目录不存在');
            }
        }
        return static::$config[$ext][$key];

    }

    /**
     * 设置配置文件扩展
     * @param $ext
     * @return EncryptionConfigHelp
     */
    public function setExt($ext){
        $this->ext = $ext;
        if($ext == 'ini' || $ext == 'php'){
            $this->configPath = env('ENCRYPTION_PATH');
        }
        return $this;
    }

    /**
     * 单例获取对象
     * @return EncryptionConfigHelp
     */
    public static function getInstance(){
        if(is_null(static::$instance)){
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 获取php的配置项
     * @author zhaoguanglai
     * @return array
     */
    public function getPhpConfig()
    {
        if(empty($this->tempPathConfig)){
            return [];
        }
        $count = count($this->tempPathConfig);
        for($i=0; $i<$count; $i++){
            $pathConfig = array_pop($this->tempPathConfig);
            //注册对象表
            $configObj = require ($pathConfig['path']);
            //获取配置项
            if(empty($pathConfig['key'])){
                $configArray =  $configObj;
            }else{
                $tempConfig = $configObj[$pathConfig['key']];

                if(!is_null($tempConfig ) && is_object($tempConfig)){
                    $configArray = (array)$tempConfig;
                }else if(empty($tempConfig)){
                    $configArray = [];
                }else{
                    $configArray = $tempConfig;
                }

            }

            if(empty($configArray)){
                continue;
            }else{
                return $configArray;
            }
        }
        return [];
    }
    /**
     * 获取需要回溯的路径
     * @author zhaoguanglai
     * @date 2018-10-31
     * @param $key
     * @return bool
     */
    private function getPath($key){
        $pathArray = explode('.', $key);
        $tempPath = static::APPLICATION_PATH.$this->configPath;
        $count = count($pathArray);
        for($i=0; $i<$count; $i++){
            $tempPath .= '/'.array_shift($pathArray);
            if(is_file($tempPath.'.'.$this->ext)){
                $this->tempPathConfig[$i]['path'] = $tempPath.'.'.$this->ext;
                $this->tempPathConfig[$i]['key'] = implode('.', $pathArray);
            }
        }
        if(empty($this->tempPathConfig)){
            return false;
        }
        return true;
    }


}