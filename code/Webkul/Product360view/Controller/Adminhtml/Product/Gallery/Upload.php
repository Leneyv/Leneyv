<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Product360view
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Product360view\Controller\Adminhtml\Product\Gallery;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Product360view Product 360 Image Upload controller.
 */
class Upload extends Action
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * File Uploader factory.
     *
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param Json $json
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        Json $json,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->json = $json;
        $this->storeManager = $storeManagerInterface;
        parent::__construct($context);
    }

    /**
     * Save #60 image in directory
     */
    public function execute()
    {
        try {
            $currentStore = $this->storeManager->getStore();
            $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $target = $this->_mediaDirectory->getAbsolutePath('/product360view');
            $fileUploader = $this->_fileUploaderFactory->create(
                ['fileId' => 'image']
            );
            $fileUploader->setAllowedExtensions(
                ['gif', 'jpg', 'png', 'jpeg']
            );
            $fileUploader->setFilesDispersion(true);
            $fileUploader->setAllowRenameFiles(true);
            $resultData = $fileUploader->save($target);
            unset($resultData['tmp_name']);
            unset($resultData['path']);
            $resultData['url'] = $mediaUrl.'product360view'.$resultData['file'];
            $resultData['file'] = $resultData['file'];
            $resultData['filename'] = $resultData['name'];
            $this->getResponse()->representJson(
                $this->json->serialize($resultData)
            );
        } catch (\Exception $e) {
            $this->getResponse()->representJson(
                $this->json->serialize(
                    [
                        'error' => $e->getMessage(),
                        'errorcode' => $e->getCode(),
                    ]
                )
            );
        }
    }
}
