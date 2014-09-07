<?php

namespace App\Controllers\Dinner;

use App\Controllers\BaseController;
use FintechFab\Models\DinnerMenuItem;
use Maatwebsite\Excel\Facades\Excel;

class DinnerController extends BaseController
{

    public $layout = 'dinner';

	/**
	 * Показывает страницу /dinner
	 *
	 * @return $this|\Illuminate\View\View
	 */
	public function dinner()
	{
		return $this->make('dinner');
    }

	/**
	 * Импортирует файл меню в базу данных
	 *
	 * @param $url string URL файла меню
	 *
	 * @return bool Если меню успешно импортировано - true, иначе - false
	 */
	public static function importMenu($url)
	{
		if ($file_name = self::downloadFile($url)) {
			Excel::load($file_name, function ($reader) {
				$reader->noHeading();
				$reader->ignoreEmpty();
				$dates = array();

				$reader->each(function ($sheet) {
					global $dates; //печаль-беда, но foreach по $reader возвращает объект PHPExcel вместо Maatwebsite\Excel\Collections\RowCollection

					$date_row = $sheet->get(0); //берем первую строку листа, чтобы извлечь дату

					//Если первая строка листа содержит дату, то делаем все блюда листа доступными в этот день
					//иначе - блюда с листа доступны во все даты, собранные с предыдущих листов
					if (preg_match('/\d+\.\d+/', $date_row[1], $matches) > 0) {
						$current_date = date("Y-m-d", strtotime($matches[0] . '.' . date("Y"))); //год берем текущий
						$dates[] = $current_date; //добавляем текущую дату в массив дат

						self::importSheet($sheet, $current_date); //импортируем лист в БД
					} else //блюда с последнего листа - доступны в любой день
					{
						foreach ($dates as $date) //блюдо будет доступно во все даты, которые были в предыдущих листах
						{
							self::importSheet($sheet, $date); //импортируем лист в БД
						}
					}
				});
			});

			unlink($file_name);
			return true;
		}

		return false;
	}

	/**
	 * Скачивает файл в директорию временных файлов
	 *
	 * @param $url string URL файла
	 *
	 * @return string|bool Если файл успешно загружен - имя файла, иначе - false
	 */
	private static function downloadFile($url)
	{
		if ($file_content = file_get_contents($url)) {
			$file_name = tempnam(sys_get_temp_dir(), 'dinner');
			if (file_put_contents($file_name, $file_content)) {
				return $file_name;
			}
			unlink($file_name);
		}

		return false;
	}

	/**
	 * Формирует массив полей для передачи в модель DinnerMenuItem
	 *
	 * @param $row_items array Ячейки строки из excel-файла
	 * @param $date string Дата, когда блюдо будет доступно для заказа
	 *
	 * @return array|bool Массив полей, в случае неудачи - false
	 */
	private static function getMenuItemFields($row_items, $date)
	{
		if (!empty($row_items[1]) && !empty($row_items[2])) //Если первые две ячейки в строке не пусты - значит в этой строке блюдо
		{
			$fields = [
				'title' => $row_items[1],
				'price' => $row_items[2],
				'date' => $date,
			];

			if (!empty($row_items[3])) $fields['description'] = $row_items[3]; //Описания может не быть

			return $fields;
		}

		return false;
	}

	/**
	 * Импортирует лист из excel-файла в БД
	 *
	 * @param Maatwebsite\Excel\Collections\RowCollection $sheet Лист
	 * @param string $date Дата, когда блюдо будет доступно для заказа
	 */
	private static function importSheet($sheet, $date)
	{
		foreach ($sheet as $row) {
			$row_items = $row->all(); //получаем ячейки текущей строки

			if ($fields = self::getMenuItemFields($row_items, $date)) //формируем массив полей для модели
				DinnerMenuItem::create($fields); //добавляем блюдо в БД
		}
	}

}