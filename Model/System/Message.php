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
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    public function __construct(
        \Magento\AdminNotification\Model\ResourceModel\Inbox\CollectionFactory $inboxFactory,
        UrlInterface $url,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->inboxFactory = $inboxFactory;
        $this->url = $url;
        $this->_escaper = $escaper;
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
            $readDetailsHtml = $notification->getUrl() ? ' <a style=\'font-size: 9pt;\' class="action-details" target="_blank" href="' .
                $this->escapeUrl($notification->getUrl())
                . '">' .
                __('Read Details') . '</a>' : '';

            $markAsReadHtml = !$notification->getIsRead() ? ' <a style=\'font-size: 9pt;\' class="action-mark" href="' . $this->getUrl(
                    'adminhtml/notification/markAsRead/',
                    ['id' => $notification->getId()]
                ) . '">' . __(
                    'Mark as Read'
                ) . '</a>' : '';
            $html .= "<div style='padding-bottom:5px; color:gray;'>" . $notification->getTitle() .
                $readDetailsHtml.
                $markAsReadHtml.
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

    public function getUrl($route = '', $params = [])
    {
        return $this->url->getUrl($route, $params);
    }

    public function escapeUrl($string)
    {
        return $this->_escaper->escapeUrl((string)$string);
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