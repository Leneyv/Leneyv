<?php

namespace WeSupply\Toolbox\Observer\Admin;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\User\Model\User;
use WeSupply\Toolbox\Api\Authorize;
use WeSupply\Toolbox\Api\WeSupplyApiInterface;
use WeSupply\Toolbox\Helper\Data as WeSupplyHelper;

class LayoutLoadBefore implements ObserverInterface
{
    /**
     *@var Curl
     */
    protected $curlClient;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var Authorize
     */
    protected $_auth;

    /**
     * @var AuthSession
     */
    protected $authSession;

    /**
     * @var WeSupplyApiInterface
     */
    protected $weSupplyApi;

    /**
     * @var WeSupplyHelper
     */
    protected $helper;

    /**
     * @var string
     */
    protected $authHash;

    /**
     * @var array
     */
    protected $curlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
    ];

    /**
     * @var array
     */
    protected $wsAuthData = [
        'ws_full_match' => '',
        'ws_admin_key'  => NULL,
        'ws_auth_hash'  => NULL,
        'ws_logged_in'  => FALSE
    ];

    /**
     * LayoutLoadBefore constructor.
     * @param Curl $curl
     * @param JsonHelper $jsonHelper
     * @param Authorize $authorize
     * @param AuthSession $authSession
     * @param WeSupplyApiInterface $weSupplyApi
     * @param WeSupplyHelper $helper
     */
    public function __construct(
        Curl $curl,
        JsonHelper $jsonHelper,
        Authorize $authorize,
        AuthSession $authSession,
        WeSupplyApiInterface $weSupplyApi,
        WeSupplyHelper $helper
    )
    {
        $this->curlClient = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->_auth = $authorize;
        $this->authSession = $authSession;
        $this->weSupplyApi = $weSupplyApi;
        $this->helper = $helper;

        if ($this->authSession->getWsAdminKey()) {
            $this->wsAuthData['ws_admin_key'] = $this->authSession->getWsAdminKey();
        }
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $actionName = $observer->getEvent()->getFullActionName();
        if ($actionName != 'sales_order_view') {
            return $this;
        }

        if (empty($this->helper->getWesupplyFullDomain())) {
            return $this;
        }

        if (
            !$this->helper->getWeSupplyEnabled() ||
            (!$this->helper->getEnableWeSupplyAdminOrder() && !$this->helper->getEnableWeSupplyAdminReturns())
        ) {
            return $this;
        }

        if ($this->checkWsIsLoggedIn()) {
            return $this;
        }

        if (!$this->performWsAdminLogin()) {
            // not authorized :: unset auto-login data
            $this->updateSessionData('uns');

            return $this;
        }

        // authorized :: set auto-login data
        $this->updateSessionData('set');

        return $this;
    }

    /**
     * @return bool
     */
    private function performWsAdminLogin()
    {
        $this->setAuthHash();
        $loginLink = $this->helper->getWesupplyFullDomain()
            . 'admin/auto-login?adminEmail=' . $this->getAdminEmail()
            . '&authBy=' . 'client_secret'
            . '&hash=' . $this->wsAuthData['ws_auth_hash'];

        $this->curlClient->setOptions($this->curlOptions);
        $this->curlClient->get($loginLink);

        $this->extractAdminKey($this->curlClient->getBody());

        return $this->checkWsIsLoggedIn();
    }

    /**
     * @return bool
     */
    private function checkWsIsLoggedIn()
    {
        if (!$this->wsAuthData['ws_admin_key']) {
            return FALSE;
        }

        $checkAdminUrl = $this->helper->getWesupplyFullDomain() . $this->wsAuthData['ws_admin_key'] . '/admin/checkAdminSignedIn';
        $this->curlClient->setOptions($this->curlOptions);
        $this->curlClient->get($checkAdminUrl);

        if ($this->curlClient->getStatus() != Http::STATUS_CODE_200) {
            return FALSE;
        }

        $response = $this->curlClient->getBody();
        if ($this->isJson($response)) {
            $response = $this->jsonHelper->jsonDecode($response);
        }

        if (isset($response['success']) && $response['success'] === TRUE) {
            $this->wsAuthData['ws_logged_in'] = TRUE;

            return TRUE;
        }

        return FALSE;
    }

    /**
     * @param $response
     */
    private function extractAdminKey($response)
    {
        preg_match('/adminkey=\'(.*?)\'/mi', $response, $matches);
        if ($matches) {
            list($this->wsAuthData['ws_full_match'], $this->wsAuthData['ws_admin_key']) = $matches;
        }
    }

    /**
     * @return User|null
     */
    private function getAdminUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * @return mixed|string|null
     */
    private function getAdminEmail()
    {
        return $this->getAdminUser()->getEmail();
    }

    /**
     * @param $string
     * @return bool
     */
    private function isJson($string)
    {
        return is_string($string)
            && is_array(json_decode($string, true))
            && (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Set encrypted auth hash
     */
    private function setAuthHash(): void
    {
        $this->wsAuthData['ws_auth_hash'] = $this->_auth->authenticate(json_encode([
            'mage_session' => $this->authSession->getSessionId()
        ]));
    }

    /**
     * @param $action
     */
    private function updateSessionData($action)
    {
        foreach ($this->wsAuthData as $key => $data) {
            $method = $this->buildMethodName($action, $key);
            switch ($action) {
                case 'set':
                    $this->authSession->{$method}($data);
                    break;
                case 'uns':
                    $this->authSession->{$method}();
                    break;
            }
        }
    }

    /**
     * @param $action
     * @param $key
     * @return string
     */
    private function buildMethodName($action, $key)
    {
        return $action . str_replace('_', '', ucwords(strtolower($key), '_'));
    }

}
