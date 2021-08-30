<?php

namespace Nusa\UploadProduct\Console\Command;
use Magento\Framework\App\Filesystem\DirectoryList;

class ProductUploadCommand extends \Symfony\Component\Console\Command\Command {
    protected $objectManager;
    protected $directory_list;
    public function __construct( \Magento\Framework\ObjectManagerInterface $objectmanager,
                                 \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
                                 $name = null
    ){
        $this->objectManager = $objectmanager;
        $this->directory_list = $directory_list;
        return parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('nusa:upload');
        $this->setDescription('Product Upload from CSV for Baytech');
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ){
        $csvPath=$this->directory_list->getPath('media')."/import/products.csv";
        $this->upload($csvPath);
    }

    private function getColumnIndexFromLine($line, $columnName){
        $i=0;
        foreach ($line as $ele) {
            if($ele==$columnName){
                return $i;
            }
            $i++;
        }
        return -1;
    }

    public function upload($csvPath) {

        if (!file_exists($csvPath)) {
            return array(0,'csv file not found');
        }

        $lineIndexArray = array(
            "subcategory" => -1,
            "sku" => -1,
            "name" => -1,
            "price" => -1,
        );

        $obj=$this->objectManager;
        $state = $obj->get('Magento\Framework\App\State');
        $state->setAreaCode('adminhtml');

        $fh = fopen($csvPath, 'r');
        $i=0;
        $log='';
        while (($line = fgetcsv($fh, 0, ",")) !== FALSE) {
            if($i<1){
                $bFailed = false;
                foreach($lineIndexArray as $key => $value){
                    $tmpIndex = $this->getColumnIndexFromLine($line,$key);
                    if($tmpIndex==-1){
                        $bFailed = true;
                        break;
                    }
                    else{
                        $lineIndexArray[$key] = $tmpIndex;
                    }
                }
                if($bFailed){
                    $log .= "Required columns are missing in csv file. Skipping to next file...";
                    break;
                }
                $i++;
                continue;
            } else {
                if (trim($line[$lineIndexArray["sku"]]) == '') {
                    var_dump('Finished Import');
                    die();
                }

                //Create Simple Product
                $sku = trim($line[$lineIndexArray["sku"]]);
                $_product = null;
                $_productCollection = $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')->addFieldToFilter('sku',array("eq" => $sku));
                if (!$_product){
                    $_product = $this->objectManager->create('\Magento\Catalog\Model\Product');
                }
                $_product->setWebsiteIds(array(1))
                    ->setAttributeSetId(4)
                    ->setSku($sku)
                    ->setTypeId('simple');
                $_product->setDescription($line[$lineIndexArray["name"]]);
                $_product->setName($line[$lineIndexArray["name"]])
                    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->setWeight(1)
                    ->setUrlKey($this->stringClean($sku.'-'.$line[$lineIndexArray["name"]]))
                    ->setPrice(floatval(trim($line[$lineIndexArray["price"]])))
                    ->setStockData(array(
                        'is_in_stock' => 1,
                        'qty' => '100',
                        'is_qty_decimal' => 1));

                $_productCategoryIds = explode(',',$line[$lineIndexArray["subcategory"]]);
                $_product->setCategoryIds($_productCategoryIds);
                try{
                    $_product->save();
                }catch(\Magento\Framework\Exception\AlreadyExistsException $ex){
                    die('An error happened in '.$sku);
                }
                echo $i.":".$_product->getId()." Saved!\n";
                $i++;
            }
        }
    }

    private function stringClean($sourceString){
        $specChar=array(
            ' ',
            ',',
            '"',
            '’',
            "'",
            '?'
        );

        //$resultString=strtolower(trim($sourceString));
        $resultString=trim($sourceString);
        foreach($specChar as $char){
            $resultString=str_replace($char,'',$resultString);
        }
        return $resultString;
    }
}