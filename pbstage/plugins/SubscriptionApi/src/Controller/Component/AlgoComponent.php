<?php
namespace SubscriptionApi\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;

class AlgoComponent extends Component
{

	public function searchProducts($param)
	{
		$this->Product = new ProductComponent(new ComponentRegistry());
		//$familyTable = TableRegistry::get('SubscriptionApi.Families');
		//$allFamily   = $familyTable->getFamilies(); //pr($allFamily); die;
		//$notesFamily = array_combine(array_column($allFamily, 'title'), array_column($allFamily, 'title'));
		$algoProductTable = TableRegistry::get('SubscriptionApi.AlgoProducts');
		$resultFinal = [];

		$familyForm = $param['families'] ?? [];
		$productFrom = $param['favoritePerfumes'] ?? [];
		$gender  = $param['gender'] ?? '';
		$brandId  = $param['brandId'] ?? '';
		$filter['Products.is_stock'] = 'in_stock';
		$filter['Products.is_active'] = 'active';
		$filter['Products.is_combo'] = '0';
		if ( !empty($param['gender']) ) {
			$gender = explode(",", $param['gender']);
			$gender[] = 'unisex';
			$gender[] = 'mfemale';
			$filter['Products.gender IN'] = $gender;
		}
		if ( !empty($brandId) && ($brandId > 0) ) {
			$filter['Products.brand_id'] = $brandId;
		}

		$productTableWith = TableRegistry::get('SubscriptionApi.Products')->find('all', ['fields' => ['id', 'url_key', 'sku_code', 'size', 'unit', 'sold', 'tag_line', 'gender', 'family_ids', 'quantity', 'discount',  'discount_from',  'discount_to', 'is_stock', 'is_combo', 'pscore'], 'conditions' => $filter])
			->innerJoinWith('ProductCategories', function ($q){ return $q->where(['category_id'=>4]); })
			->contain([
				'ProductPrices' => [
					'queryBuilder' => function ($q) {
						return $q->select(['id', 'product_id', 'name', 'title', 'price', 'short_description'])->where(['ProductPrices.location_id' => 1, 'ProductPrices.is_active' => 'active']);
					}
				],
				'ProductPrices.Locations' => [
					'queryBuilder' => function ($q) {
						return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
					}
				],
				'ProductNotes' => [
					'queryBuilder' => function ($q) {
						return $q->select(['product_id', 'title', 'description'])->where(['ProductNotes.is_active' => 'active'])->order(['title'=>'DESC']);
					}
				],
				'Brands' => [
					'queryBuilder' => function ($q) {
						return $q->select(['id', 'title', 'description', 'image'])->where(['Brands.is_active' => 'active']);
					}
				],
				'ProductCategories.Categories' => [
					'queryBuilder' => function ($q) {
						return $q->select(['id', 'name', 'url_key']);
					}
				],
				'ProductImages' => [
					'queryBuilder' => function ($q) {
						return $q->select(['product_id', 'title', 'alt'=>'alt_text', 'small' => 'img_small', 'base' => 'img_base', 'large' => 'img_large'])->where(['ProductImages.exclude' => '0', 'ProductImages.is_active' => 'active'])->order(['img_order'=>'ASC']);
					}
				]
			])->hydrate(0)->toArray();

		$totalFamilyEarnValue = 0;
		$matched = [];
		$resultFinal = [];
		if ( !empty($productFrom) ) {
			$topNotePer =  40;
			$baseNotePer = 30;
			//$productFrom = implode(",", $productFrom); pr($productFrom); die;
			$prodFrom =	$algoProductTable->find('All')->where(['id IN' => $productFrom])->hydrate(0)->toArray();
			foreach ( $prodFrom as $resFrom ) {
				$pyramidFrom =  json_decode($resFrom['perfume_pyramid']);
				$topNoteNameFrom = [];
				$middleNoteNameFrom = [];
				$baseNoteNameFrom = [];
				$topNoteNameFromNds = [];
				$middleNoteNameFromNds = [];
				$baseNoteNameFromNds = [];
				if ( isset($pyramidFrom->topNote) ) {
					$familyName = [];
					foreach ( $pyramidFrom->topNote as $value ) {
						$noteName = trim($value->name); //top notes title
						if ( !empty($noteName) ) {
							$topNoteNameFromNds[] = strtolower($noteName);
							$familyName[] = $noteName; 
						}
					}
					$topNoteNameFrom = $this->getFamilyName($familyName);//return family name
				}
				if ( isset($pyramidFrom->middleNote) ) {
					$familyName = [];
					foreach ($pyramidFrom->middleNote as $value) {
						$noteName = trim($value->name); //middle notes title
						if ( !empty($noteName) ) {
							$middleNoteNameFromNds[] = strtolower($noteName);
							$familyName[] = $noteName;
						}
						$familyName = $this->getFamilyName($noteName);//return family name
						if ( !empty($familyName) ) {
							$middleNoteNameFrom[] = $familyName;
						}
					}
					$middleNoteNameFrom = $this->getFamilyName($familyName);//return family name
				}
				if ( isset($pyramidFrom->baseNote) ) {
					$familyName = [];
					foreach ($pyramidFrom->baseNote as $value) {
						$noteName = trim($value->name); //base notes title
						if ( !empty($noteName) ) {
							$baseNoteNameFromNds[] = strtolower($noteName);
							$familyName[] = $noteName;
						}
					}
					$baseNoteNameFrom = $this->getFamilyName($familyName);//return family name
				}
				$top_P_MiddNoteNameFrom = array_merge($topNoteNameFrom, $middleNoteNameFrom);
				$midd_P_BaseNoteNameFrom = array_merge($middleNoteNameFrom, $baseNoteNameFrom);
				$notesMergeAllFrom = array_merge($topNoteNameFromNds, $middleNoteNameFromNds, $baseNoteNameFromNds);

				foreach ($productTableWith as $resWith) { //Products Table Data
					$price = $resWith['product_prices'][0]['price'];
					if( !empty($resWith['product_prices']) && ($price > 0) ) { //pr($resWith); die;
						$topMiddleBaseNotes = $this->getNoteByProduct($resWith['id']);
						$topNoteNameWith = $this->getFamilyName($topMiddleBaseNotes['top']);
						$topNoteNameWithNds = $topMiddleBaseNotes['top'];
						$middleNoteNameWith = $this->getFamilyName($topMiddleBaseNotes['middle']);
						$middleNoteNameWithNds = $topMiddleBaseNotes['middle'];
						$baseNoteNameWith = $this->getFamilyName($topMiddleBaseNotes['base']);
						$baseNoteNameWithNds = $topMiddleBaseNotes['base'];
						
						$top_P_MiddNoteNameWith = array_merge($topNoteNameWith, $middleNoteNameWith);
						$midd_P_BaseNoteNameWith = array_merge($middleNoteNameWith, $baseNoteNameWith);
						$notesMergeAllWith = array_merge($topNoteNameWithNds, $middleNoteNameWithNds, $baseNoteNameWithNds); 
						//pr($top_P_MiddNoteNameWith); die;
						//Note Comparing
						$topMiddleToTopMiddle     =   array_unique(array_intersect($top_P_MiddNoteNameFrom, $top_P_MiddNoteNameWith));
						$middleBaseToMiddleBase   =   array_unique(array_intersect($midd_P_BaseNoteNameFrom, $midd_P_BaseNoteNameWith));

						$totalMatchNotes = 0;
						if (!empty($topMiddleToTopMiddle)) {
							$matchTopValue =  $this->getPercentSum($top_P_MiddNoteNameFrom, $top_P_MiddNoteNameWith, $topMiddleToTopMiddle);
							$totalMatchNotes +=  $this->giveAdditionalPercent($matchTopValue, $topNotePer);
						}

						if (!empty($middleBaseToMiddleBase)) {
							$matchBaseValue = $this->getPercentSum($midd_P_BaseNoteNameFrom, $midd_P_BaseNoteNameWith, $middleBaseToMiddleBase);
							$totalMatchNotes +=     $this->giveAdditionalPercent($matchBaseValue, $baseNotePer);
						}
						$getNotesMatchValueCmp = [];
						if (!empty($notesMergeAllFrom) &&  !empty($notesMergeAllWith)) {
							$getNotesMatchValueCmp =   $this->getNotesValueByMatch($notesMergeAllFrom, $notesMergeAllWith);
						}
						if (!empty($getNotesMatchValueCmp))
							$totalMatchNotes += $getNotesMatchValueCmp['matchvalue'];
						//$totalMatchNotes = (($totalMatchNotes != '') ? ($totalMatchNotes) : '0');
						//End Note Comparing
						//$totalMatchNotesAndFaml = $totalMatchNotes;
						$TotalPointsCollected = round((($totalMatchNotes + $resWith['pscore']) / 2), 2);
						//$TotalPointsCollected = $totalMatchNotesAndFaml;
						$takeAllFamily = array_merge($topNoteNameWith, $middleNoteNameWith, $baseNoteNameWith); 
						$divtakeAllNoteNameWith =   1;
						$takeAllPercentWith     =   $this->dividendPer($takeAllFamily, $matched, $divtakeAllNoteNameWith);
						$fnlArrSort = [];
						foreach ($takeAllPercentWith as $key => $value) {
							$fnlArrSort[] = ['name' => $key, 'value' => $value, 'rank' => $value];
						}
						usort($fnlArrSort, function ($a, $b) {
							return $b <=> $a;
						});
						$fnlarrper = [];
						$topNoteFamilyPer = '';
						foreach ($fnlArrSort as $key => $value) {
							switch ($value['value']) {
								case 1: $topNoteFamilyPer = 90; break;
								case 2: $topNoteFamilyPer = 80; break;
								default: $topNoteFamilyPer = 70;
							}
							$percntfamily = ($value['value'] / count($takeAllFamily));
							$fnlarrper[] = ['name' => $value['name'], 'value' => $percntfamily, 'rank' => $value['rank']];
						}
						
						$topFamilyHold =  array_slice($fnlarrper, 0, 5);
						if (count($familyForm) == 1 && count($topFamilyHold) >= 1) {
							if (count($topFamilyHold) >= 1) {
								$yourArray = array_map('strtolower', $familyForm);
								foreach ($topFamilyHold as $key => $value) {
									if (in_array(strtolower($value['name']), $yourArray)) {
										switch ($value['rank']) {
											case 1: $topNoteFamilyPer = 70; break;
											case 2: $topNoteFamilyPer = 80; break;
											default: $topNoteFamilyPer = 90;
										}
										$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
									}
								}
							}
							$famscore = $this->getFamilyScore($familyForm);
							if (count($famscore) > 1) {
								$fscval = round(array_sum($famscore) / count($famscore), 2);
							} else {
								$fscval = array_sum($famscore);
							}
						} else if (count($familyForm) > 1) {
							$rtyu = [];
							$fscval = '';
							$topNoteFamilyPer = 0;
							$topFamilyHoldArr = array_column($topFamilyHold, 'name');
							$hhArr =  array_intersect(array_map('strtolower', $topFamilyHoldArr), array_map('strtolower', $familyForm)); //pr($topFamilyHoldArr);pr($familyForm);pr($hhArr);
							switch (count($hhArr)) {
								case 1: $topNoteFamilyPer = 80; break;
								case 2: $topNoteFamilyPer = 90; break;
								default: $topNoteFamilyPer = 100;
							}
							$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
							$famscore = $this->getFamilyScore($hhArr);	
							if (count($famscore) > 1) {
								$fscval = round(array_sum($famscore) / count($famscore), 2);
							} else {
								$fscval = array_sum($famscore);
							}
						}
						$totalFamilyEarnValue = $rtyu[0]['EarnValue'] ?? 0;
						// fields selections
						$categories = array_map(function ($item) {
							return $item['category'];
						}, $resWith['product_categories']);
						$images = array_map(function($item){
							array_splice($item, 0, 1);
							return $item;
						}, $resWith['product_images']);
						$notes = $this->Product->manageNotes($resWith['product_notes']);
						$discount = $this->Product->discountValidity($price, $resWith['discount'], $resWith['discount_from'], $resWith['discount_to']);
						$price = $discount['price'] ?? $price;
						if ( count($images) == 0 ) { $images = $this->Product->images; }
						$resultFinal[] = [
							'id' => $resWith['id'],
							'name' => $resWith['product_prices'][0]['title'],
							'title' => $resWith['product_prices'][0]['title'],
							'shortDescription' => !empty($resWith['product_prices'][0]['short_description']) ? $resWith['product_prices'][0]['short_description']:'N/A',
							'urlKey' => $resWith['url_key'],
							'sku' => $resWith['sku_code'],
							'tagClass' => $this->Product->getTagClass($resWith['tag_line']),
							'tagLine' => empty($resWith['tag_line']) ? '' : $resWith['tag_line'],
							'price' => $price,
							'priceLogo' => $resWith['product_prices'][0]['price_logo'],
							'discount' => $discount,
							'quantity' => $resWith['quantity'],
							'gender' => $resWith['gender'],
							'brand' => $resWith['brand'],
							'categories' => $categories,
							'notes' => $notes,
							'size' => $resWith['size'],
							'unit' => $resWith['unit'],
							'sold' => $resWith['sold'],
							'isStock' => ($resWith['quantity'] > 0) ? $resWith['is_stock'] : 'out_of_stock',
							'family' => ['fscore'=>round(($resWith['pscore'] + $totalFamilyEarnValue + $totalMatchNotes)/3, 2)],
							'oos_image' =>PC['OOS_IMAGE'],
							'images' => $images,
							'value' => $TotalPointsCollected,
							//'score' => $resWith['pscore'],
							//'FamilyEarnValue' => $totalFamilyEarnValue,
							//'notesMatchValue' => $getNotesMatchValueCmp['matchvalue'] ?? 0,
							//'totalMatchNotes' => $totalMatchNotes
						];
					}
				} //end of foreach
			} //end of foreach
		} else {
			foreach ($productTableWith as $resWith) {				
				if( !empty($resWith['product_prices']) ) { //pr($resWith); die;
					$rtyu = [];
					$fnlArrSort = [];
					$topNoteFamilyPer = 0;
					$totalMatchNotes = 0;
					$topMiddleBaseNotes = $this->getNoteByProduct($resWith['id']);
					$topNoteNameWith = $this->getFamilyName($topMiddleBaseNotes['top']);
					$middleNoteNameWith = $this->getFamilyName($topMiddleBaseNotes['middle']);
					$baseNoteNameWith = $this->getFamilyName($topMiddleBaseNotes['base']);
					$takeAllFamily = array_merge($topNoteNameWith, $middleNoteNameWith, $baseNoteNameWith); 
					$divtakeAllNoteNameWith =   1;
					$takeAllPercentWith     =   $this->dividendPer($takeAllFamily, $matched, $divtakeAllNoteNameWith);
					foreach ($takeAllPercentWith as $key => $value) {
						$fnlArrSort[] = ['name' => $key, 'value' => $value, 'rank' => $value];
					}
					usort($fnlArrSort, function ($a, $b) {
						return $b <=> $a;
					});
					$fnlarrper = [];
					foreach ($fnlArrSort as $key => $value) {
						switch ($value['value']) {
							case 1: $topNoteFamilyPer = 90; break;
							case 2: $topNoteFamilyPer = 80; break;
							default: $topNoteFamilyPer = 70;
						}
						$percntfamily = ($value['value'] / count($takeAllFamily));
						$fnlarrper[] = ['name' => $value['name'], 'value' => $percntfamily, 'rank' => $value['rank']];
					}
					$topFamilyHold =  array_slice($fnlarrper, 0, 5);
					if (count($familyForm) == 1 && count($topFamilyHold) >= 1) {
						$yourArray = array_map('strtolower', $familyForm);
						foreach ($topFamilyHold as $key => $value) {
							if (in_array($value['name'], $yourArray)) {
								switch ($value['rank']) {
									case 1: $topNoteFamilyPer = 70; break;
									case 2: $topNoteFamilyPer = 80; break;
									default: $topNoteFamilyPer = 90;
								}
								$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
							}
						}
						$famscore = $this->getFamilyScore($familyForm);
						if (count($famscore) > 1) {
							$fscval = round(array_sum($famscore) / count($famscore), 2);
						} else {
							$fscval = array_sum($famscore);
						}
					} else if (count($familyForm) > 1) {
						$topFamilyHoldArr = array_column($topFamilyHold, 'name');
						$hhArr =  array_intersect($topFamilyHoldArr, $familyForm);
						switch (count($hhArr)) {
							case 1: $topNoteFamilyPer = 80; break;
							case 2: $topNoteFamilyPer = 90; break;
							default: $topNoteFamilyPer = 100;
						}
						$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
						$famscore = $this->getFamilyScore($hhArr);
						if (count($famscore) > 1) {
							$fscval = round(array_sum($famscore) / count($famscore), 2);
						} else {
							$fscval = array_sum($famscore);
						}
					}
					$totalFamilyEarnValue = ($rtyu ? $rtyu[0]['EarnValue'] : 0);
					// Fields selection
					$categories = array_map(function ($item) {
						return $item['category'];
					}, $resWith['product_categories']);
					$images = array_map(function($item){
						array_splice($item, 0, 1);
						return $item;
					}, $resWith['product_images']);
					$notes = $this->Product->manageNotes($resWith['product_notes']);
					$price = $resWith['product_prices'][0]['price'] ?? 0;
					$discount = $this->Product->discountValidity($price, $resWith['discount'], $resWith['discount_from'], $resWith['discount_to']);
					$price = $discount['price'] ?? $price;
					if ( count($images) == 0 ) { $images = $this->Product->images; }
					$TotalPointsCollected = round((($totalFamilyEarnValue + $resWith['pscore']) / 2), 2);
					$resultFinal[] = [
						'id' => $resWith['id'],
						'name' => $resWith['product_prices'][0]['title'],
						'title' => $resWith['product_prices'][0]['title'],
						'shortDescription' => !empty($resWith['product_prices'][0]['short_description']) ? $resWith['product_prices'][0]['short_description']:'N/A',
						'urlKey' => $resWith['url_key'],
						'sku' => $resWith['sku_code'],
						'tagClass' => $this->Product->getTagClass($resWith['tag_line']),
						'tagLine' => empty($resWith['tag_line']) ? '' : $resWith['tag_line'],
						'price' => $price,
						'priceLogo' => $resWith['product_prices'][0]['price_logo'],
						'discount' => $discount,
						'quantity' => $resWith['quantity'],
						'gender' => $resWith['gender'],
						'brand' => $resWith['brand'],
						'categories' => $categories,
						'notes' => $notes,
						'size' => $resWith['size'],
						'unit' => $resWith['unit'],
						'sold' => $resWith['sold'],
						'isStock' => ($resWith['quantity'] > 0) ? $resWith['is_stock'] : 'out_of_stock',
						'family' => ['fscore'=>$TotalPointsCollected],
						'oos_image' =>PC['OOS_IMAGE'],
						'images' => $images,
						'value' => $TotalPointsCollected
						//'score' => $resWith['pscore'],
						//'notesMatchValue' => $getNotesMatchValueCmp['matchvalue'] ?? 0, 
						//'allFamily' => $fnlarrper, 
						//'FamilyEarnValue' => $totalFamilyEarnValue, 
						//'baseNoteNameWith' => $baseNoteNameWith, 
						//'middleNoteNameWith' => $middleNoteNameWith, 
						//'topNoteNameWith' => $topNoteNameWith,  
						//'totalMatchNotes' => ''
					];
				}
			}
		}
		// usort($resultFinal,[$this->Product,'compareOrder']);
		usort($resultFinal, function ($a, $b) {
			return $b['value'] <=> $a['value'];
		});
		$response = $ids = [];
		foreach ( $resultFinal as $value ) {
			if ( !in_array($value['id'], $ids) ) {
				$ids[] = $value['id'];
				$response[] = $value;
			}
		}
		return $response;
	}

	public 	function getFamilyScore($title)
	{
		$response = [];
		if ( !empty($title) ) {
			$query = TableRegistry::get('SubscriptionApi.Families')->find('all', ['fields'=>['fscore']])->where(['title IN' => $title, 'is_active' => 'active'])->hydrate(0)->toArray();
			$response = array_column($query, 'fscore');
		}
		return $response;
	}
	
	public function getFamilyName($notes)
	{
		$response = [];
		if ( !empty($notes) ) {
			$query = TableRegistry::get('SubscriptionApi.AlgoNotes')->find('all', ['fields'=>['group_name']])->where(['note_name IN' => $notes, 'status' => 1])->limit(1)->hydrate(0)->toArray();
			$response = array_column($query, 'group_name');
		}
		return $response;
	}

	public	function getNoteByProduct($productId)
	{
		$top = $middle = $base = $response = []; //$noteTitle = 'middle_note';
		$query = TableRegistry::get('SubscriptionApi.ProductNotes')->find('all', ['fields'=>['title','description']])->where(['product_id'=>$productId])->hydrate(0)->toArray();
		foreach ( $query as $value ) {
			$temp = explode(",", $value['description']);
			switch ($value['title']) {
				case 'top_note': $top = array_merge($top, $temp); break;
				case 'middle_note': $middle = array_merge($middle, $temp); break;
				case 'base_note': $base = array_merge($base, $temp); break;
				default:
			}
		}
		$response = ['top'=>array_map('strtolower',$top), 'middle'=>array_map('strtolower',$middle), 'base'=>array_map('strtolower',$base)];
		return $response;
	}

	public function getPercentSum($from, $to, $matched)
	{
		$divFrom = round((100 / count($from)), 2);
		$divTo = round((100 / count($to)), 2);

		$eachPercentFrom = $this->dividendPer($from, $matched, $divFrom);

		$eachPercentTo = $this->dividendPer($to, $matched, $divTo);
		return $this->compareListSumPercentage($eachPercentFrom, $eachPercentTo, $matched);
	}

	public function compareListSumPercentage($arrayFrom = null, $arrayTo = null, $matched = null)
	{
		$matchFrom = [];
		foreach ($matched as $key) {
			if (array_key_exists($key, $arrayFrom)) {
				$matchFrom[$key] = $arrayFrom[$key];
			}
		}

		$matchTo = [];
		foreach ($matched as $key) {
			if (array_key_exists($key, $arrayTo)) {
				$matchTo[$key] = $arrayTo[$key];
			}
		}

		$c = array_map(function (...$arrays) {
			return array_sum($arrays);
		}, $matchFrom, $matchTo);

		return (array_sum($c) / 2);
	}

	public function giveAdditionalPercent($baseValue, $addnValue)
	{
		return round((($baseValue * $addnValue) / 100), 2);
	}

	public function getNotesValueByMatch($notesFrom, $notesWith)
	{
		$matchArr = array_unique(array_intersect($notesFrom, $notesWith));
		switch ( count($matchArr) ) {
			case 1: $value = 10; break;
			case 2: $value = 15; break;
			case 3: $value = 20; break;
			case 4: $value = 25; break;
			case 5: $value = 30; break;
			default:  $value = 35;
		}
		return ['matchNotes' => $matchArr, 'matchvalue' => $value];
	}
	
	public function dividendPer($array, $matched, $divby)
	{
		$totArr = [];
		$totArrs = array_count_values($array);
		foreach ($totArrs as $key => $value) {
			$totArr[$key] = $value * $divby;
		}
		return $totArr;
	}
	
}
