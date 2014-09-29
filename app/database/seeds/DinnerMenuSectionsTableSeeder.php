<?php
/**
 * Created by PhpStorm.
 * User: mantisa1824
 * Date: 14.09.14
 * Time: 13:23
 */

class DinnerMenuSectionsTableSeeder extends Seeder{

	public function run(){

		$sections = Array('Первые блюда', 'Вторые блюда', 'Гарниры', 'Салаты', 'Десерты');

		for($i = 0 , $cnt = count($sections); $i < $cnt; $i++){
			$data = array(
				array(
					'title' => $sections[$i],
				)
			);

			DB::table('dinner_menu_sections')->insert($data);
		}

	}

} 