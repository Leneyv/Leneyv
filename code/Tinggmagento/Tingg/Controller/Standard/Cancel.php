<?php

namespace Tinggmagento\Tingg\Controller\Standard;

class Cancel extends \Tinggmagento\Tingg\Controller\Checkout
{

    public function execute()
    {
        $this->_cancelPayment();
        $this->_checkoutSession->restoreQuote();
        $this->getResponse()->setRedirect(
            $this->getCheckoutHelper()->getUrl('checkout')
        );
    }

}
