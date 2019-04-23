<?php
/**
 * Created by PhpStorm.
 * User: ducquach
 * Date: 4/22/19
 * Time: 7:10 PM
 */
namespace Magenest\Core\Model\System;

use Magento\AdminNotification\Model\ResourceModel\Inbox\Collection;
use Magento\Backend\Model\UrlInterface;

class Message implements \Magento\Framework\Notification\MessageInterface
{
    protected $inboxFactory;
    protected $notifications;
    protected $url;

    public function __construct(
        \Magento\AdminNotification\Model\ResourceModel\Inbox\CollectionFactory $inboxFactory,
        UrlInterface $url
    ) {
        $this->inboxFactory = $inboxFactory;
        $this->url = $url;
    }

    public function getIdentity()
    {
        return 'MAGENEST_NOTIFICATION';
    }

    public function isDisplayed()
    {
        return $this->getCollection()->getSize();
    }

    public function getText()
    {
        $html = null;
        foreach ($this->getCollection() as $notification) {
            $html .= "<div style='padding-bottom:5px; color:gray;'>" . $notification->getTitle() .
                " <a style='font-size: 9pt;' href='{$notification->getUrl()}'>(Read Details)</a>" .
                " <a style='font-size: 9pt;' href='{$this->url->getUrl('admin/notification/markAsRead',[ 'id' => $notification->getId()])}'>(Mark as Read)</a>".
                "</div>";
        }
        if (!empty($html)) {
            $html = "<div style='font-weight:bold;'>Magenest Extension Notifications</div>".$html;
        }
        return $html;
    }

    public function getSeverity()
    {
        return self::SEVERITY_NOTICE;
    }

    /**
     * @return Collection
     */
    private function getCollection()
    {
        if (is_null($this->notifications)) {
            /** @var Collection $inbox */
            $inbox = $this->inboxFactory->create();
            $inbox->addFieldToFilter('is_magenest', 1)
                ->addFieldToFilter('is_read', 0)
                ->addOrder('date_added')
                ->setPageSize(5)
                ->setCurPage(1)
                ->load();
            $this->notifications = $inbox;
        }
        return $this->notifications;
    }
}