<?php

class Stasevi4_Autoupsell_Model_Collection extends Mage_Core_Model_Abstract {

	public function getUpsellProducts($limit = false) {
	
		$product = Mage::registry('current_product');
		$products = $product->getUpSellProducts();

		if (!$products) {
			$level  = 'level ASC';
		
			if ($product ) {				
				if (Mage::getStoreConfig('autoupsell/general/samecategory') == 1) {				
					$level  = 'level DESC';		
				}
				$categoryIds = $product->getCategoryIds();
				
				if (empty($categoryIds)) {
					return false;		
				}
				$cats_collection = Mage::getModel('catalog/category')->getCollection()
					->addAttributeToFilter('entity_id', $categoryIds)
					->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
					->addAttributeToFilter('is_active', 1);
				$cats_collection->getSelect()->order($level )->limit(1);
				$cats_collection->load();

				foreach($cats_collection as $category){
						$catid = $category->getId();
				}
				
				if (!empty($catid )) {
					$category = Mage::getModel('catalog/category')->load($catid);			
				}
			
			}

			if (isset($category)) {
				if ($limit === false) {
					$limit = Mage::getStoreConfig('autoupsell/general/limit');
				}
				
				$products = Mage::getResourceModel('reports/product_collection')
					->addAttributeToFilter('visibility', array(
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
					))
					->addAttributeToFilter('status', 1)
					->addCategoryFilter($category)
					->addAttributeToSelect('*')
					->addStoreFilter()
					->addUrlRewrite()
					->setPageSize($limit);
					
					
				if($this->getData('related_products')) {
					$products->addAttributeToFilter('entity_id', array(
						'neq' => $this->relatedCollection->getAllIds())
					);
				}
				 
				if ($product) {
					$products->addAttributeToFilter('entity_id', array(
						'neq' => Mage::registry('current_product')->getId())
					);
				}

				$products->getSelect()->order(new Zend_Db_Expr('RAND()'));
				Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($products);

			} else {
				return false;
			}
		}
		
		return $products;
	}
	
	public function getRelatedProducts($limit = false){
		
		$products = $this->getData('related_products');
				
		if (!$products) {
			$product = Mage::registry('current_product');						
			if ($product) {

				$categoryIds = $product->getCategoryIds();
				if (empty($categoryIds)) {
					return false;		
				}

				$cats_collection = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToFilter('entity_id', $categoryIds)
				->addFieldToFilter('store_id', Mage::app()->getStore()->getId())
				->addAttributeToFilter('is_active', 1);

				$cats_collection->getSelect()->order('level DESC')->limit(1);
				$cats_collection->load();

				foreach($cats_collection as $category){

				}
				

			}
	
			if (isset($category)) {
				if ($limit === false) {
					$limit = Mage::getStoreConfig('autoupsell/generalrelated/limit');
				}
				$products = Mage::getResourceModel('reports/product_collection')
					->addAttributeToFilter('visibility', array(
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
					))
					->addAttributeToFilter('status', 1)
					->addCategoryFilter($category)
					->addAttributeToSelect('*')
					->addStoreFilter()
					->setPageSize($limit);

		
				if( Mage::getSingleton('core/session')->getUpsellProductIds()) {
					$products->addAttributeToFilter('entity_id', array(
						'nin' => array_values(Mage::getSingleton('core/session')->getUpsellProductIds()))
					);
					
				}	
					
				if ($product) {
					$products->addAttributeToFilter('entity_id', array(
						'neq' => Mage::registry('current_product')->getId())
					);
				}

				$products->getSelect()->order(new Zend_Db_Expr('RAND()'));
				Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($products);
				
				$this->setData('related_products', $products);
			} else {
				return false;
			}
		}
		return $products;
	}

}
 ?>