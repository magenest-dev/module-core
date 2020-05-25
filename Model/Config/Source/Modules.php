<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Core\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magenest\Core\Helper\Data;

/**
 * Class Modules
 * @package Magenest\Core\Model\Config\Source
 */
class Modules implements OptionSourceInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Modules constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->helper->getModulesWithVersion();
    }
}