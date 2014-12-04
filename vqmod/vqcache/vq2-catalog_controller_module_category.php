<?php  
class ControllerModuleCategory extends Controller {
	protected function index($setting) {
		$this->language->load('module/category');

		$this->data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$this->data['category_id'] = $parts[0];
		} else {
			$this->data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$this->data['child_id'] = $parts[1];
		} else {
			$this->data['child_id'] = 0;
		}

 
				if (isset($parts[2])) {
			$this->data['ch3_id'] = $parts[2];
		} else {
			$this->data['ch3_id'] = 0;
		}	
				

			//cachemanager
			$cacheconfig = $this->config->get('cache');
			$language_id = (int)$this->config->get('config_language_id');
			$cache_data = array();
			
			if ($cacheconfig['categorymodule']['status']) {
				if ($cacheconfig['categorymodule']['status']) {
					$cache_data  = $this->cache->get('module.categorymodule');
				};
			}
									
			if (!empty($cache_data[$language_id])) {
				$this->data['categories'] = $cache_data[$language_id];
				} else {			
			//cachemanager						
			
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		 
				foreach ($categories as $category) {
			$children_data = array();
				
				$children = $this->model_catalog_category->getCategories($category['category_id']);
				
				foreach ($children as $child) {
					
					$level3 = $this->model_catalog_category->getCategories($child['category_id']);
					$l3_data = array();
					foreach ($level3 as $l3) {
						$data = array(
						'filter_category_id'  => $l3['category_id'],
						'filter_sub_category' => true	
					);		
					    $product_total = $this->model_catalog_product->getTotalProducts($data);
						if ($this->config->get('config_product_count')) {
						$l3_data[] = array(
							'category_id' => $l3['category_id'],
							'name'        => $l3['name']. ' (' . $product_total . ')',
							'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']. '_' . $l3['category_id'])
						);
						} else {
						$l3_data[] = array(
							'category_id' => $l3['category_id'],
							'name'        => $l3['name'],
							'href'        => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']. '_' . $l3['category_id'])
						);
						}
					}
					$data1 = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true	
					);
					$product_total = $this->model_catalog_product->getTotalProducts($data1);
					if ($this->config->get('config_product_count')) {
						$children_data[] = array(
							'category_id' => $child['category_id'],
							'name'  => $child['name']. ' (' . $product_total . ')',
							'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
							'children3'    => $l3_data
						);
					} else {
						$children_data[] = array(
							'category_id' => $child['category_id'],
							'name'  => $child['name'],
							'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
							'children3'    => $l3_data
						);	
					}
				}
				// Level 1
				$data2 = array(
						'filter_category_id'  => $category['category_id'],
						'filter_sub_category' => true	
					);
				$product_total = $this->model_catalog_product->getTotalProducts($data2);
				if ($this->config->get('config_product_count')) {
				$this->data['categories'][] = array(
					'name'     => $category['name']. ' (' . $product_total . ')',
					'children' => $children_data,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
					'category_id' => $category['category_id']
				);					
				} else {
					$this->data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
					'category_id' => $category['category_id']
				);
				}
			}
				
































			if ($cacheconfig['categorymodule']['status']) {
			$cache_data[$language_id] = $this->data['categories'];
			$this->cache->set('module.categorymodule', $cache_data, (int)$cacheconfig['categorymodule']['lifetime'], (int)$this->config->get('gzip'));
			}
		}

			
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/category.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/category.tpl';
		} else {
			$this->template = 'default/template/module/category.tpl';
		}

		$this->render();
	}
}
?>