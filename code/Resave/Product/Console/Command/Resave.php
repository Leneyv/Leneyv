<?php
declare(strict_types=1);

namespace Resave\Product\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\ObjectManager;


class Resave extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";
    protected $objectManager;
    protected $subject;

     public function __construct(\Magento\Framework\App\State $state,\Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $subject)
     {
        $this->state = $state;
        
        $this->productUrlPathGenerator = $subject;
        $this->objectManager = ObjectManager::getInstance();
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) { 
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $name = $input->getArgument(self::NAME_ARGUMENT);
        $option = $input->getOption(self::NAME_OPTION);
        
        $productCollectionFactory = $this->objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory'); 
        $productcollection = $productCollectionFactory->create()->addAttributeToSelect('*')
                            ->addAttributeToFilter('entity_id', array( 'from' => $name))
                            ->load();
         $output->writeln("Total Products".$productcollection->getSize().'\r');
         

        foreach ($productcollection as $product) {
            $productId = $product->getId();
            //$manufacturer = $product->getAttributeText('manufacturer');
            $mpn =''; 
            $manufacturer =''; 
            if ($product->getData('manufacturer') !== null){
                $manufacturer = $product->getAttributeText('manufacturer');
            }
            if ($product->getData('mpn') !== null){
                $mpn = $product->getAttributeText('mpn');
            } 
 
           // $mpn = $product->getAttributeText('mpn');
            //$output->writeln("product productUrlPathGenerator -".$this->productUrlPathGenerator->getUrlKey($product).'\r');
            if(empty($manufacturer) && empty($mpn)){
                $url =  $product->getId();
            }elseif(empty($mpn)){
                $url = $manufacturer;
            }elseif(empty($manufacturer)){
                $url = $mpn;
            }else{
                $url = $manufacturer.'-'.$mpn;
            }
            //$output->writeln("product manufacturer  ".$manufacturer.'\r');
            $output->writeln("product url ".$url.'\r');
            $product = $this->objectManager->create('Magento\Catalog\Model\Product');
            $product->load($productId); 
            $product->setUrlKey($url); 
            $product->save(); 
            $output->writeln("product saved successfully -".$productId.'\r');
        }
        $output->writeln("successfully End ");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("resave_product:resave");
        $this->setDescription("resave product");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}

