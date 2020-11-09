<?php

class VS7_CatalogSearchSort_Model_Observer
{
    public function sortByStock($observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
        if ($productCollection instanceof Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection &&
            in_array('catalogsearch_result_index', $handles)) {
            $query = Mage::helper('catalogsearch')->getQuery();
            if ($query->getNumResults() > 0) {
                $select = $productCollection->getSelect();
                $select->join(
                    'cataloginventory_stock_item',
                    'cataloginventory_stock_item.product_id = e.entity_id',
                    array('is_in_stock'));
                $orders = $select->getPart(Zend_Db_Select::ORDER);
                $orderStrFull = '';
                foreach ($orders as $order) {
                    $orderStrFull .= $order[0] . ' ' . $order[1];
                }
                $select->reset(Zend_Db_Select::ORDER);
                $select->order('is_in_stock DESC');
                $select->order($orderStrFull);
            }
        }
    }
}