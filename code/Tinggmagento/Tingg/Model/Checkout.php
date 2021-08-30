<?php

namespace Tinggmagento\Tingg\Model;

use Magento\Quote\Model\Quote\Payment;
use Tinggmagento\Tingg\Model\Ecryption;

class Checkout extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'Tingg_payment';
    protected $_code = self::CODE;
    protected $_isGateway = false;
    protected $_isOffline = false;
    protected $_canRefund = true;
    protected $_isInitializeNeeded = false;
    protected $helper;
    protected $_minAmount = null;
    protected $_maxAmount = null;
    protected $_secretKey = null;
    protected $_ivKey = null;
    protected $_serviceCode = null;
    protected $_payment_period = 400;
    protected $_accessKey = null;
    public $_Tinggurl = "";
    protected $_supportedCurrencyCodes = array(
        "KES", "TZS", "UGX", "XOF", "USD", "ZWS", "NGN"
    );
    protected $_formBlockType = 'Tinggmagento\Tingg\Block\Form\Checkout';
    protected $_infoBlockType = 'Tinggmagento\Tingg\Block\Info\Checkout';

    protected $httpClientFactory;
    protected $orderSender;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Tinggmagento\Tingg\Helper\Checkout $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
    )
    {
        $this->helper = $helper;
        $this->orderSender = $orderSender;
        $this->httpClientFactory = $httpClientFactory;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

        $this->_minAmount = $this->getConfigData('min_order_total');
        $this->_maxAmount = $this->getConfigData('max_order_total');
        $this->_Tinggurl = $this->getConfigData('checkoutUrl');
        $this->_secretKey = $this->getConfigData('secretKey');
        $this->_ivKey = $this->getConfigData('ivKey');
        $this->_serviceCode = $this->getConfigData('serviceCode');
        $this->_accessKey = $this->getConfigData('accessKey');
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && (
                $quote->getBaseGrandTotal() < $this->_minAmount
                || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    public function buildCheckoutRequest($quote)
    {
        $billing_address = $quote->getBillingAddress();

        $params = array();
        $params["sid"] = $this->getConfigData("vendor_id");
        $params["merchant_order_id"] = $quote->getReservedOrderId();
        $params["cart_order_id"] = $quote->getReservedOrderId();
        $params["currency_code"] = $quote->getOrderCurrencyCode();
        $params["total"] = round($quote->getGrandTotal(), 2);
        $params["card_holder_name"] = $billing_address->getName();
        $params["street_address"] = $billing_address->getStreet()[0];
        if (count($billing_address->getStreet()) > 1) {
            $params["street_address2"] = $billing_address->getStreet()[1];
        }
        $params["city"] = $billing_address->getCity();
        $params["state"] = $billing_address->getRegion();
        $params["zip"] = $billing_address->getPostcode();
        $params["country"] = $billing_address->getCountryId();
        $params["email"] = $quote->getCustomerEmail();
        $params["phone"] = $billing_address->getTelephone();
        $params["return_url"] = $this->getCancelUrl();
        $params["x_receipt_link_url"] = $this->getReturnUrl();
        $params["purchase_step"] = "payment-method";

        return $params;
    }

    public function validateResponse($orderNumber, $total, $key)
    {
        $secretWord = $this->getConfigData('hash_key');
        $merchantId = $this->getConfigData('vendor_id');

        $stringToHash = strtoupper(md5($secretWord . $merchantId . $orderNumber . $total));
        if ($stringToHash != $key) {
            $result = false;
        } else {
            $result = true;
        }
        return $result;
    }

    public function postProcessing(\Magento\Sales\Model\Order $order, \Magento\Framework\DataObject $payment, $response)
    {
        // Update payment details
        $payment->setTransactionId($response['id']);
        $payment->setIsTransactionClosed(0);
        $payment->setTransactionAdditionalInfo('Tingg_order_number', $response['id']);
        $payment->setAdditionalInformation('Tingg_order_number', $response['id']);
        $payment->setAdditionalInformation('Tingg_order_status', 'approved');
        $payment->place();


        // Update order status
        $order->setStatus($this->getOrderStatus());
        $order->setExtOrderId($response['id']);
        $order->save();

        // Send email confirmation
        $this->orderSender->send($order);
    }

    public function getCgiUrl($quote)
    {

        $live = $this->getConfigData('sandbox') ? '0' : '1';

        $autopay = $this->getConfigData('autopay') ? '1' : '0';
        //$autopay = 1;

        $billing_address = $quote->getBillingAddress();

        $mm = 1;
        $mb = 1;
        $dc = 1;
        $cc = 1;

        $oid = $quote->getReservedOrderId();
        $inv = $quote->getReservedOrderId();
        $ttl = $quote->getGrandTotal();
        $tel = $billing_address->getTelephone();
        $eml = $quote->getCustomerEmail();
        $fisrt_name = $billing_address->getFirstname() . PHP_EOL;
        $last_name = $billing_address->getLastname();

        /**
         * incase of any dashes in the telephone number the code below removes them
         * @var [string]
         */
        $tel = str_replace("-", "", $tel);
        $tel = str_replace(array(' ', '<', '>', '&', '{', '}', '*', "+", '!', '@', '#', "$", '%', '^', '&'), "", $tel);

        $vid = $this->getConfigData('vendor_id');
        //$vid = 'test';

        $curr = "";

        if (in_array($quote->getQuoteCurrencyCode(), $this->_supportedCurrencyCodes)) {
            $curr = $quote->getQuoteCurrencyCode();
        } else {
            echo "Unsupported currency";
            exit();
        }

        if (in_array($curr, $this->_supportedCurrencyCodes) && $curr != "USD") {
            $ttl = ceil($ttl);
        }


        /**
         * $p1, $p2, $p3, $p4  are optional fields. Allow sending & receiving your custom parameters
         * Each option should not exceed 20 characters.
         */
        $p1 = '';
        $p2 = '';
        $p3 = '';
        $p4 = '';

        /**
         * [$callbk holds the callback URL]
         */
        $callbk = $this->getReturnUrl();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $linkbk = $storeManager->getStore()->getBaseUrl();

        /*URLENCODE*/
        $cbk = urlencode($callbk);
        $lbk = urlencode($linkbk);

        $checkout = new Ecryption($this->_secretKey, $this->_ivKey);
        $encryptedPayload = $checkout->encrypt([
            "merchantTransactionID" => $oid,
            "customerFirstName" => $fisrt_name,
            "customerLastName" => $last_name,
            "MSISDN" => $tel,
            "customerEmail" => $eml,
            "requestAmount" => $ttl,
            "currencyCode" => "KES",
            "accountNumber" => $inv,
            "serviceCode" => $this->_serviceCode,
            "dueDate" => date("Y-m-d H:i:s", strtotime("+" . $this->_payment_period . " minutes")),
            "requestDescription" => $oid,
            "countryCode" => "KE",
            "languageCode" => "EN",
            "successRedirectUrl" => "http://www.test.com",
            "failRedirectUrl" => "http://www.test.com",
            "paymentWebhookUrl" => ""
        ]);


        $checkout_payment_url = sprintf(
            $this->_Tinggurl. "?params=%s&accessKey=%s&countryCode=%s",
            $encryptedPayload,
            $this->_accessKey,
            "KE"
        );

        return $checkout_payment_url;
    }

    public function getRedirectUrl()
    {
        $url = $this->helper->getUrl($this->getConfigData('redirect_url'));
        return $url;
    }

    public function getReturnUrl()
    {
        $url = $this->helper->getUrl($this->getConfigData('return_url'));
        return $url;
    }

    public function getCancelUrl()
    {
        $url = $this->helper->getUrl($this->getConfigData('cancel_url'));
        return $url;
    }

    public function getOrderStatus()
    {
        $value = $this->getConfigData('order_status');
        return $value;
    }

    public function getVendorID()
    {
        return $this->getConfigData('vendor_id');
    }

    public function getMerchantCountry()
    {
        return $this->getConfigData('merchant_country');
    }
}
