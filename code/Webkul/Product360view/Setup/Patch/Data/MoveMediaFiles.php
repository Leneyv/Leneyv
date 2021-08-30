<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Product360view
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Product360view\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class MoveMediaFiles implements DataPatchInterface
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $reader;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Module\Dir\Reader $reader
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Module\Dir\Reader $reader
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->reader = $reader;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $this->processDefaultImages();
    }

    /**
     * Copy Banner and Icon Images to Media
     */
    private function processDefaultImages()
    {
        $error = false;
        try {
            $this->createDirectories();
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $ds = "/";
            $baseModulePath = $this->reader->getModuleDir('', 'Webkul_Product360view');
            $mediaDetails = [
                "product360view/thumbnail" => [
                    "view/base/web/images/product360view/thumbnail" => [
                        "360_image_thumbnail.png"
                    ]
                ]
            ];

            foreach ($mediaDetails as $mediaDirectory => $imageDetails) {
                foreach ($imageDetails as $modulePath => $images) {
                    foreach ($images as $image) {
                        $path = $directory->getAbsolutePath($mediaDirectory);
                        $mediaFilePath = $path.$ds.$image;
                        $moduleFilePath = $baseModulePath.$ds.$modulePath.$ds.$image;

                        if ($this->file->fileExists($mediaFilePath)) {
                            continue;
                        }

                        if (!$this->file->fileExists($moduleFilePath)) {
                            continue;
                        }

                        $this->file->cp($moduleFilePath, $mediaFilePath);
                    }
                }
            }
        } catch (\Exception $e) {
            $error = true;
        }
    }

    /**
     * Create default directories
     */
    private function createDirectories()
    {
        $mediaDirectories = ['product360view/thumbnail'];
        foreach ($mediaDirectories as $mediaDirectory) {
            $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $path = $directory->getAbsolutePath($mediaDirectory);
            if (!$this->file->fileExists($path)) {
                $this->file->mkdir($path, 0777, true);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }
}
