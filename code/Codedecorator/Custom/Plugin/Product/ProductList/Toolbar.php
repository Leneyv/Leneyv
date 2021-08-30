<?php
namespace Codedecorator\Custom\Plugin\Product\ProductList;

class Toolbar
{

    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $collection
    ) {

        $currentOrder = $subject->getCurrentOrder();
        $currentDirection = $subject->getCurrentDirection();
        $result = $proceed($collection);

        if ($currentOrder) {
            if ($currentOrder == 'new') {
                if($currentDirection == 'asc'){
                    $subject->getCollection()->setOrder('created_at', 'desc');

                }else{
                    $subject->getCollection()->setOrder('created_at', 'asc');
                }
                //$subject->getCollection()->setOrder('created_at', $currentDirection);
            }
            if ($currentOrder == 'old') {

                /*if($currentDirection == 'asc'){
                    $subject->getCollection()->setOrder('created_at', 'desc');

                }else{
                    $subject->getCollection()->setOrder('created_at', 'asc');
                }*/
                $subject->getCollection()->setOrder('created_at', $currentDirection);
            }
        }

        return $result;
    }
}