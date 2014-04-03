<?php



class Stasevi4_Autoupsell_Block_Crosssell extends Mage_Checkout_Block_Cart_Crosssell  {

	 public function getItems(){

		parent::getItems();
		$items = $this->getData('items');			

		$enable = Mage::getStoreConfig("autoupsell/generalcrosssell/enabled");
		
		if($enable){
			if (count($items)<1) {
				$items = array();
				$limit = Mage::getStoreConfig("autoupsell/generalcrosssell/limit");
				if($limit<1){
					$limit = $this->_maxItemCount;
				}
							
				$ninProductIds = Mage::getResourceModel('reports/product_collection')
						->addAttributeToFilter('visibility', array(
									Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
									Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
						))
						->addAttributeToFilter('status', 1)
						->addAttributeToSelect('*')
						->addStoreFilter()
						->addUrlRewrite()										
						->setPageSize($limit);
													
				$ninProductIds->getSelect()->order(new Zend_Db_Expr('RAND()'));
				Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($ninProductIds);

				foreach ($ninProductIds as $item) {
							$items[] = $item;
				}
					
					$this->setData('items', $items);	
				
			}
		}
	  return $items;
	}
}
 


?>