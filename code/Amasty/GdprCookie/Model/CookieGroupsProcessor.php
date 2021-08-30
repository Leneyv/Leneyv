<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_GdprCookie
 */


namespace Amasty\GdprCookie\Model;

use Amasty\GdprCookie\Model\Repository\CookieGroupsRepository;
use Amasty\GdprCookie\Model\Repository\CookieRepository;
use Amasty\GdprCookie\Model\ResourceModel\CookieDescription\Collection as CookieDescription;
use Amasty\GdprCookie\Model\ResourceModel\CookieGroupDescription\Collection as CookieGroupDescription;
use Amasty\GdprCookie\Model\ResourceModel\CookieGroupLink\Collection;

class CookieGroupsProcessor
{
    /**
     * @var CookieGroupsRepository
     */
    private $cookieGroupsRepository;
    /**
     * @var Collection
     */
    private $groupLink;
    /**
     * @var CookieRepository
     */
    private $cookieRepository;
    /**
     * @var CookieDescription
     */
    private $cookieDescription;
    /**
     * @var CookieGroupDescription
     */
    private $cookieGroupDescription;
    /**
     * @var CookieManager
     */
    private $cookieManager;

    public function __construct(
        CookieGroupsRepository $cookieGroupsRepository,
        Collection $groupLink,
        CookieRepository $cookieRepository,
        CookieDescription $cookieDescription,
        CookieGroupDescription $cookieGroupDescription,
        CookieManager $cookieManager
    ) {
        $this->cookieGroupsRepository = $cookieGroupsRepository;
        $this->groupLink = $groupLink;
        $this->cookieRepository = $cookieRepository;
        $this->cookieDescription = $cookieDescription;
        $this->cookieGroupDescription = $cookieGroupDescription;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAllGroups($storeId)
    {
        $groups = $this->cookieGroupsRepository->getAllGroups();
        $result = [];
        $allowAll = false;
        $selectedGroups = explode(',', $this->cookieManager->getAllowCookies());

        if (in_array('0', $selectedGroups)) {
            $allowAll = true;
        }

        foreach ($groups as $group) {
            if (!$group->getIsEnabled()) {
                continue;
            }

            $storeGroupDescription =
                $this->cookieGroupDescription->getDescriptionByStore($group->getId(), $storeId);
            $groupId = $group->getId();
            $checked = in_array($groupId, $selectedGroups) || $allowAll;
            $groupData = [];
            $groupData['description'] = $storeGroupDescription->getData('description')
                ? : $group->getDescription();
            $groupData['name'] = $storeGroupDescription->getData('name') ? : $group->getName();
            $groupData['isEssential'] = (bool)$group->getIsEssential();
            $groupData['cookies'] = [];
            $groupData['groupId'] = $groupId;
            $groupData['checked'] = $checked || (bool)$group->getIsEssential();

            $linkedCookies = $this->groupLink->getCookiesByGroup($group->getId());

            foreach ($linkedCookies as $linkedCookie) {
                $cookie = $this->cookieRepository->getById($linkedCookie->getData('cookie_id'));
                $storeCookieDescription =
                    $this->cookieDescription->getDescriptionByStore($cookie->getId(), $storeId);
                $cookieDescription = $storeCookieDescription->getData('description') ? : $cookie->getDescription();

                array_push(
                    $groupData['cookies'],
                    [
                        'name' => $cookie->getName(),
                        'description' => $cookieDescription,
                        'lifetime' => $cookie->getLifetime()
                    ]
                );
            }

            $result[] = $groupData;
        }

        return $result;
    }
}
