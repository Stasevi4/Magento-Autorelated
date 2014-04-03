<?php


class Stasevi4_Autoupsell_Block_Upsell extends Mage_Catalog_Block_Product_List_Upsell  {
	
	protected function _construct() {
		
		$_time = 3600;
		if($_time > 0 && $cacheKey = $this->_cacheKey()) {
			$this->addData(array(
				'cache_lifetime'    => $_time,
				'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
				'cache_key'         => $cacheKey,
			));
		}
    }

    protected function _cacheKey(){
		$product = Mage::registry('current_product');
		if($product) {
			return get_class() . '::' .  Mage::app()->getStore()->getCode() . '::' . $product->getId();
		}
		
		return false;
    }

	protected function _prepareData (){
		parent::_prepareData();

		$_enabled = Mage::getStoreConfig('autoupsell/general/enabled');
			
		if ($_enabled && count($this->getItems()) == 0){

			$_products = Mage::getModel('autoupsell/collection')->getUpsellProducts();
		
			if ($_products){
			
				$this->_itemCollection = $_products->load();
					
				$I= 0;
				  
				foreach ($this->_itemCollection as $product) {
					$this->_items[$I] =$product;
					$array_ids[$I]= $product->getId();
					$I++;
				}
				if(isset($array_ids)){
					Mage::getSingleton('core/session')->setUpsellProductIds($array_ids);
				}
			}
		}
		
		$columncount = Mage::getStoreConfig('autoupsell/general/columncount') ;
		if( $columncount && $columncount>0 ){
		
			$this->setColumnCount($columncount);
		}

		return $this;
	}
	 
	 
}


?>