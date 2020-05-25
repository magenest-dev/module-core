<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Core\Block\Adminhtml\System\Config\Form\Button;

use Magento\Config\Block\System\Config\Form\Field as ConfigFormField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Backend\Block\Template\Context;
use Magenest\Core\Helper\Data;

/**
 * Class SendFeedback
 * @package Magenest\Core\Block\Adminhtml\System\Config\Form\Button
 */
class SendFeedback extends ConfigFormField
{
    protected $_buttonLabel = 'Send';

    /**
     * @var string
     */
    protected $_version;

    protected $_edition;

    /**
     * SendFeedback constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        Context $context,
        array $data = []
    ) {
        $this->_version = $productMetadata->getVersion();
        $this->_edition = $productMetadata->getEdition();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return string
     */
    public function getEdition()
    {
        return $this->_edition;
    }

    /**
     * @return string
     */
    public function getFeedbackUrl()
    {
        return Data::getModuleFeedbackUrl();
    }

    /**
     * @return $this|ConfigFormField
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/feedback.phtml');
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel  = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_buttonLabel;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id'      => $element->getHtmlId(),
                'data-ui-id'   => "page-actions-toolbar-save-button"
            ]
        );

        return $this->_toHtml();
    }
}