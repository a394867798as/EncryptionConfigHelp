<?php
/**
 * 获取配置-以目录为优先级
 * @author zhaoguanglai
 * @date 2018-2-12
 * @param key 需要查找的 application.directory
 */
if ( ! function_exists('ency_config')) {
    function ency_config($key = 'application', $type='php') {
        return \HxsdHelp\Help\EncryptionConfigHelp::getInstance()->setExt($type)->get($key);

    }
}