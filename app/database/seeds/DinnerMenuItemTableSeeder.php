<?php
/**
 * Created by PhpStorm.
 * User: mantisa1824
 * Date: 04.09.14
 * Time: 15:21
 */

class DinnerMenuItemTableSeeder extends Seeder{

	public function run(){
		return array(
			array(
				'title' => 'Блюдо № 1',
				'description' => 'салат Греческий; семга отварная с овощами; картофельное пюре с добавлением натурального сливочного масла; хлеб пшеничный; хлеб ржаной; Вилка столовая одноразовая; Нож столовый одноразовый',
				'price' => 120,
				'date' => '',
				'deleted' => false,
			)
		);
	}
} 