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
namespace Webkul\Product360view\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Product360view\Model\Product360viewFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem;

class AfterProductSave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var Product360viewFactory
     */
    protected $product360viewFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Filesystem
     */
    protected $mediaDirectory;
    
    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Product360viewFactory $product360viewFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param File $file
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        Product360viewFactory $product360viewFactory,
        StoreManagerInterface $storeManagerInterface,
        File $file,
        Filesystem $filesystem
    ) {
        $this->_request = $request;
        $this->product360viewFactory = $product360viewFactory;
        $this->storeManager = $storeManagerInterface;
        $this->file = $file;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
    }

    /**
     * update data after product save
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->_request->getParams();
        $storeId = $this->storeManager->getStore()->getId();
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        $mediaDir = $this->mediaDirectory->getAbsolutePath('/product360view');

        if (!isset($data['product']['media_gallery_360'])) {
            return;
        }
        $image360data = $data['product']['media_gallery_360']['images'];
        $imageContent = [];
        $count = 0;
        $position = 0;
        foreach ($image360data as $set => $data) {
            $check = false;
            list($imageContents, $position) =
                $this->setImageContent($data, $mediaDir, $count, $position, $productId, $storeId);
            $position = $position;
            if (!empty($imageContents)) {
                array_push($imageContent, $imageContents);
            }
            $count++;
        }

        $product360collection = $this->product360viewFactory->create()->getCollection()
            ->addFieldToFilter('entity_id', ['eq' => $productId]);

        if ($product360collection->getSize() > 0) {
            $product360collection->walk('delete');
            $model = $this->product360viewFactory->create();
            foreach ($imageContent as $key) {
                foreach ($key as $data) {
                    $model->setData($data)->save();
                }
            }
            return;
        } else {
            $model = $this->product360viewFactory->create();
            foreach ($imageContent as $key) {
                foreach ($key as $data) {
                    $model->setData($data)->save();
                }
            }
            return;
        }
    }
    
    /**
     * Set Image Content Array
     *
     * @param array $data
     * @param string $mediaDir
     * @param int $count
     * @param int $position
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function setImageContent($data, $mediaDir, $count, $position, $productId, $storeId)
    {
        $imageContent = [];
        $check = false;
        foreach ($data as $key => $value) {
            if ($key == 'removed' && $value == '1') {
                $check = true;
            } else {
                
                switch ($key):
                    case 'file':
                        if ($check) {
                            $this->file->deleteFile($mediaDir.$value);
                            break;
                        }
                        $imageContent[$count][$key] = $value;
                        break;
                    case 'position':
                        if ($check) {
                            break;
                        }
                        $imageContent[$count][$key] = $position;
                        $imageContent[$count]['entity_id'] = $productId;
                        $imageContent[$count]['store_id'] = $storeId;
                        $position++;
                endswitch;
            }
        }
        $count++;

        return [$imageContent, $position];
    }
}
