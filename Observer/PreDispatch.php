<?php
/**
 * @author Magenest Team
 * @copyright Copyright (c) 2018 Magenest (https://www.magenest.com)
 * @package Magenest_Core
 */
namespace Magenest\Core\Observer;

use Magenest\Core\Helper\Data;
use Magento\Framework\Event\ObserverInterface;

class PreDispatch implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendSession;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * PreDispatch constructor.
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Data $helper
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $helper
    ) {
        $this->backendSession = $backendAuthSession;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->backendSession->isLoggedIn()) {
            if($this->helper->checkUpdate()){
                try{
                    $curlClient = $this->helper->getCurlClient();
                    $modules = $this->helper->getModules();
                    $curlClient->setOption(CURLOPT_REFERER, $this->_storeManager->getStore()->getBaseUrl());
                    $curlClient->post(Data::getUpdateUrl(),$modules);
                } catch (\Exception $e){
                }

                try {
                    $this->helper->checkNotificationUpdate();
                } catch (\Exception $e) {
                }
            }
        }
    }

}
