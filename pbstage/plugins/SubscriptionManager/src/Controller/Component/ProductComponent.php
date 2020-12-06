<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Collection\Collection;

class ProductComponent extends Component
{
	public function getProductImages ( $id = 0 ) {
		$data = [];
		$query = TableRegistry::get('SubscriptionManager.ProductImages')->find('all', ['fields'=>['id', 'product_id', 'img_order', 'title', 'alt_text', 'img_thumbnail', 'img_small', 'img_base', 'img_large', 'is_thumbnail', 'is_small', 'is_base', 'is_large','exclude','is_active'],'conditions'=>['product_id'=>$id]])->toArray();
		foreach ( $query as $v ) {
			$data[] = [
				'id'=>$v->id,
				'imgOrder'=>$v->img_order,
				'title'=>$v->title,
				'alt'=>$v->alt_text,
				'imgThumbnail'=>$v->img_thumbnail,
				'imgSmall'=>$v->img_small,
				'imgBase'=>$v->img_base,
				'imgLarge'=>$v->img_large,
				'isThumbnail'=>$v->is_thumbnail,
				'isSmall'=>$v->is_small,
				'isBase'=>$v->is_base,
				'isLarge'=>$v->is_large,
				'exclude'=>$v->exclude,
				'isActive'=>$v->is_active,
			];
		}
		return $data;
	}
	
	public function getFamilyName($notes)
	{
		$response = [];
		if ( !empty($notes) ) {
			$algoNotes = TableRegistry::get('SubscriptionManager.AlgoNotes')->find('all', ['fields'=>['group_name']])->where(['note_name IN' => $notes, 'status' => 1])->limit(1)->hydrate(0)->toArray();
			$response = array_column($algoNotes, 'group_name');
		}
		return $response;
	}
	public	function getNoteByProduct($productId)
	{
		$top = $middle = $base = $response = []; //$noteTitle = 'middle_note';
		$productNotes = TableRegistry::get('SubscriptionManager.ProductNotes')->find('all', ['fields'=>['title','description']])->where(['product_id'=>$productId])->hydrate(0)->toArray();
		foreach ( $productNotes as $value ) {			
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

	public 	function getFamilyScore($title)
	{
		$response = [];
		if ( !empty($title) ) {
			$query = TableRegistry::get('SubscriptionManager.Families')->find('all', ['fields'=>['fscore']])->where(['title IN' => $title, 'is_active' => 'active'])->hydrate(0)->toArray();
			$response = array_column($query, 'fscore');
		}
		return $response;
	}
	
}
