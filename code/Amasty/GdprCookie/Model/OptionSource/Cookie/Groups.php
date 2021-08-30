<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Model\OptionSource\Cookie;

use Amasty\GdprCookie\Model\Repository\CookieGroupsRepository;
use Magento\Framework\Data\OptionSourceInterface;

class Groups implements OptionSourceInterface
{
    /**
     * @var CookieGroupsRepository
     */
    private $cookieGroupsRepository;

    public function __construct(
        CookieGroupsRepository $cookieGroupsRepository
    ) {
        $this->cookieGroupsRepository = $cookieGroupsRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $groups = [['value' => "0", 'label' => __('None')]];
        $allGroups = $this->cookieGroupsRepository->getAllGroups();

        foreach ($allGroups as $group) {
            array_push($groups, ['value' => $group->getId(), 'label' => $group->getName()]);
        }

        return $groups;
    }
}
