<?php
namespace Magecomp\Orderonwhatsapp\Block;

use Magecomp\Orderonwhatsapp\Helper\Data as HelperData;
use Magecomp\Orderonwhatsapp\Helper\Mobile as HelperMobile;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Orderonwhatsapp extends Template
{
    protected $_frameworkRegistry;
    protected $_helperData;
	protected $_helperMobile;

    public function __construct(Context $context, 
        Registry $frameworkRegistry, 
        HelperData $helperData,
		HelperMobile $helperMobile, 
        array $data = [])
    {
        $this->_frameworkRegistry = $frameworkRegistry;
        $this->_helperData = $helperData;
        $this->_helperMobile = $helperMobile;
        parent::__construct($context, $data);
    }

	public function _prepareLayout() {
		return parent::_prepareLayout();
    }

    public function getProductShareBtn()
    {
        try
        {
            if($this->_helperData->isEnabled()):
                $isbtnshow = 1;
                if($this->_helperData->isEnabledForMobile()):
                    if(!($this->_helperMobile->isMobile() || $this->_helperMobile->isTablet())):
                        $isbtnshow = 0;
                    endif;
                endif;
                if($isbtnshow):

                    $currentUrl = $this->_urlBuilder->getCurrentUrl();
                    $btlyurl = $this->_helperData->getBitlyUrl($currentUrl);
                    $currentproduct = $this->_frameworkRegistry->registry('current_product');
                    $html = "";
                    $html .= $this->_helperData->getDefaultTitle();
                    $html .= "%0a";
                    $html .= "%0a";
                    $html .= "Product Name ::" . $currentproduct->getName();
                    $html .= "%0a";
                    $html .= "%0a";
                    $html .= "Product Url ::" .$btlyurl;
                    $html .= "%0a";
                    $html .= "%0a";

                    $html = html_entity_decode($html,ENT_XHTML);

                    $adminmobnum = $this->_helperData->getAdminNumber();
                    if($adminmobnum != ''):
                        $size = $this->_helperData->getSize();
                        $url = "https://api.whatsapp.com/send?phone="."$adminmobnum"."&text="."$html";
                        $returntext = '';
                        $returntext .= "<a target='_blank $adminmobnum' href='".$url."'";

                        if($size == 'small')
                        {
                            $returntext .= "class='wa_btn wa_btn_s'>Share</a>";
                        }
                        elseif($size == 'medium')
                        {
                            $returntext .= "class='wa_btn wa_btn_m'>Share</a>";
                        }
                        else
                        {
                            $returntext .= "class='wa_btn wa_btn_l'>Share</a>";
                        }
                        return $returntext;
                    endif;
                endif;
            endif;
        }
        catch(\Exception $e)
        {

        }
    }
}