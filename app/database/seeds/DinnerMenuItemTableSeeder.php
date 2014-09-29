<?php
/**
 * Created by PhpStorm.
 * User: mantisa1824
 * Date: 04.09.14
 * Time: 15:21
 */

class DinnerMenuItemTableSeeder extends Seeder{

	public function run(){

		for($i = 0 , $section = 1; $i < 25; $i++) {

			if( !($i % 5) && $i > 1 ){
				$section++;
			}

			$price = rand(100 , 500);

			$data = array(
				array(
					'title'       => 'Блюдо № ' .$i,
					'description' => 'салат Греческий; семга отварная с овощами; картофельное пюре с добавлением натурального сливочного масла; хлеб пшеничный; хлеб ржаной; Вилка столовая одноразовая; Нож столовый одноразовый',
					'price'       => $price,
					'date'        => '',
					'deleted_at'     => false,
					'section_id' => $section,
					/*'date' => ,*/
				)
			);

			DB::table('dinner_menu_items')->insert($data);
		}
	}
} 