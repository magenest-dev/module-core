<?php
/**
 * @author Magenest Team
 * @copyright Copyright (c) 2018 Magenest (https://www.magenest.com)
 * @package Magenest_Core
 */

namespace Magenest\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const BASE_URL  = 'https://store.magenest.com/';
    const BASE_CHECKUPDATE_PATH = 'magenestcore/checkupdate';
    const CACHE_MODULE_IDENTIFIER = 'magenest_modules';
    const CACHE_TIME_IDENTIFIER = 'magenest_time';
    const SEC_DIFF = 86400;

    protected $curlClient;

    protected $cache;

    protected $moduleList;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        parent::__construct($context);
        $this->cache = $cache;
        $this->curlClient = $curl;
        $this->moduleList = $moduleList;
    }

    public function checkUpdate()
    {
        $lastSt = $this->cache->load(self::CACHE_TIME_IDENTIFIER);
        if(!$lastSt){
            $this->cache->save(time(), self::CACHE_TIME_IDENTIFIER);
            return true;
        }
        if ((time() - intval($lastSt)) > self::SEC_DIFF){
            $this->cache->save(time(), self::CACHE_TIME_IDENTIFIER);
            return true;
        }else{
            return false;
        }
    }

    public function getModules()
    {
        $data = $this->cache->load(self::CACHE_MODULE_IDENTIFIER);
        if (!$data) {
            $data = $this->refreshModuleData();
        }
        $result = json_decode($data, true);

        return $result;
    }

    public function getCurlClient()
    {
        if (!$this->curlClient) {
            $this->curlClient = new \Magento\Framework\HTTP\Client\Curl();
        }
        $this->curlClient->setTimeout(2);
        return $this->curlClient;
    }

    public static function getUpdateUrl(){
        return self::BASE_URL . self::BASE_CHECKUPDATE_PATH;
    }

    private function refreshModuleData()
    {
        $moduleInfo = $this->getModulesInfo();
        if($moduleInfo){
            $this->cache->save(json_encode($moduleInfo), self::CACHE_MODULE_IDENTIFIER);
        }
        return json_encode($moduleInfo);
    }

    private function getModulesInfo()
    {
        $modulesOut = [];

        $modules = $this->moduleList->getAll();
        foreach ($modules as $module) {
            $moduleName = @$module['name'];
            if (strstr($moduleName, 'Magenest_') === false
                || $moduleName === 'Magenest_Core'
            ) {
                continue;
            }

            $modulePart = explode("_", $moduleName);
            $mName = @$modulePart[1];
            $modulesOut[] = $mName;
        }

        return $modulesOut;
    }
}
