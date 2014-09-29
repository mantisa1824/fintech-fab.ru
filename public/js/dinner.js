/**
 * Created by mantisa1824 on 06.09.14.
 */
$(document).ready(function() {

	// Доступ к меню закрыт
	if ($('#end-dinner').length) {

		// Запуск часов
		var time = createTime();
		time.updateTime();

	} else {

		// Страница с меню

		// Заказ
		var order = createOrder();

		// Обнавление заказа
		$('.less , .more').click(order.updateOrder);

		// Показ заказа в модальном окне
		$('#showOrder').click(order.showOrder);

		// Очистить заказ
		$('#modal-order .btn-default').click(order.clearOrder);

		// При клике на модальном окне на закрты
		$('#modal-order .close').click(function() {
			$('#floatingBlock').fadeIn(200);;
		});

		// Смена меню ( Секций )
		$('#sections li').click(order.changeSection);


	}
});

// Создание часов на странице где доступ к заказу закрыт
function createTime() {

	// Спан на странице куда вставляем часы
	var timeInPage = $('#show-time');

	return (function() {

		var time = {

			// Формируем часы
			showTime: function() {

				var hour, min, sec, time = new Date;

				hour = time.getHours() < 10 ? '0' + time.getHours() : time.getHours();
				min = time.getMinutes() < 10 ? '0' + time.getMinutes() : time.getMinutes();
				sec = time.getSeconds() < 10 ? '0' + time.getSeconds() : time.getSeconds();

				// Если в процессе показа страницы настало 8 утра
				// Перезагружаем страницу
				if (hour == 8) redirect(window.location.href);

				// Вставляем часы на страницу
				timeInPage.text(hour + ' : ' + min + ' : ' + sec);
			},

			// Показ и обнавление часов
			updateTime: function() {
				var timerId;

				// Если timerId не определен сразу запускаем time.showTime()
				if (timerId === undefined)
					time.showTime();

				// в интервале 1 сек вызываем time.showTime()
				timerId = setInterval('time.showTime()', 1000);
			}

		};

		return time;

	}());
}

// Перенаправление , перезагрузка страницы
function redirect(path) {
	window.location = path;
}

// Создание заказа
function createOrder() {

	var order = {
		userId: $('#user-id').val(), // При инициализации сразу записываем id юзера
		summa: 0,
		order: {}
	};

	var floatingBlock = function() {

		// Количество и сумма для отрисовки на странице
		var quantity = 0,
			summa = 0;

		// Если блока с заказаом нет то создаем
		if (!$('#floatingBlock').length) {

			// Создаем блок с информацией по заказу
			$('body').append(
				'<div id="floatingBlock">' +
					'<ul>' +
						'<li id="quantity"></li>' +
						'<li id="summa"></li>' +
						'<li id="showOrder" data-toggle="modal" data-target="#modal-order">Просмотр заказа</li>' +
						'<li id="saveOrder">Оформить заказ</li>' +
					'</ul>' +
				'</div>');

			$('#showOrder').bind({
				click: orderObject.showOder
			});
			$('#saveOrder').bind({
				click: orderObject.saveOrder
			});

			// Показываем блок
			$('#floatingBlock').fadeIn(500);

		}

		// Собираем данные из заказа , количество блюд и сумму
		for (var key in order.order) {
			quantity += order.order[key].quantity;
			summa += order.order[key].cost;
		}

		// Если количество так и осталось 0 , закрываем блок и удаляем
		if (!quantity) {
			$('#floatingBlock').fadeOut(500, function() {
				$('#floatingBlock').remove();
				return;
			});

		}

		// Если дошли до этого блока обновляем данные нв странице
		$('#floatingBlock #quantity').text('Количество блюд в заказе : ' + quantity);
		$('#floatingBlock #summa').text('Сумма заказа : ' + summa + '.00');

	};

	return (function() {

		orderObject = {
			updateOrder: function() {

				var workBlock, foodId, cost, quantity;

				// Получаем рабочую облать блюда , tr в котором был совершен клик
				workBlock = $(this).parents('tr');

				// Получаем id блюда
				foodId = workBlock.attr('class').slice(workBlock.attr('class').lastIndexOf('-') + 1);

				// Получаем стоимость блюда
				cost = workBlock.find('.cost').text();

				// Работаем с заказом в зависимости от действия которое совершил юзе
				// Добавить или убрать блюдо
				// Если юзер добавляет блюдо
				if (~$(this).attr('class').indexOf('more')) {

					// Если в заказе нет этого блюда , создаем и даем значение 1
					if (!(foodId in order.order)) {
						order.order[foodId] = {};
						order.order[foodId].quantity = 1;
					} else {
						// Если есть прибавляем еще 1
						order.order[foodId].quantity++;
					}

				} else {

					// Если действие убрать блюдо
					// проверяем его на существование и отнимаем 1
					if (foodId in order.order) {
						order.order[foodId].quantity--;
					}

				}

				// Если блюда не существует завершаем скрипт
				if (!(foodId in order.order)) return;

				// Записываем стоимость блюда
				order.order[foodId].cost = cost * order.order[foodId].quantity;

				// Меняем количество в заказе данного блюда на странице
				workBlock.find('.quantity').text(order.order[foodId].quantity);

				// Работа с плавающим блоком
				// Информация о заказе на странице
				floatingBlock();

				// Если юзер полностью убрал блюдо из заказа  осталось 0
				// Удаляем его
				!order.order[foodId].quantity && delete order.order[foodId];

			},

			// Показ закза
			showOder: function() {

				// При скрываем плавающий блок внизу страницы
				$('#floatingBlock').fadeOut(200);

				// Очищаем тело модального окна
				$('#modal-order .modal-body').empty();

				// Генерируем таблицу
				$('#modal-order .modal-body').append(
					'<table class="table table-hover">' +
						'<thead>' +
							'<tr>' +
								'<th>Название блюда</th>' +
								'<th>Количество порций</th>' +
								'<th>Сумма</th>' +
							'</tr>' +
						'</thead>' +
					'<tbody class="tbody-active">'
				);

				// Данные о заказе
				var dataOrder = order.order;

				// В цикле извлекаем данные о заказе
				for (var key in dataOrder) {

					// Получаем название текущего блюда со страницы
					var foodName = $('.this-food-id-' + key).find('.first').text();

					// генерируем ряд с текущим блюдом в таблице
					$('#modal-order .modal-body table').append(
						'<tr class="this-food-id-' + key + '">' +
							// Название блюда
							'<td>' + foodName + '</td>' +
							'<td>' +
							// Количество в заказе
							'<span class="quantity">' + dataOrder[key].quantity + '</span>&nbsp;' +
							'</td>' +
							// Сумма по текущему блюду
							'<td>' + dataOrder[key].cost + '.00</td>' +
						'</tr>'
					);
				}

				// Закрываем таблицу 
				$('#modal-order .modal-body').append(
						'</tbody>' +
					'</table>' +
					// Стоимость всего заказа
					'<div class="pull-right">' + $('#floatingBlock #summa').text() + '</div>'
				);
			},

			// Очищаем заказ
			clearOrder: function() {

				// Получаем доступ ко всем спанам с количеством 
				var quantity = $('.quantity');

				// В цикле обнуляем каждый
				quantity.each(function() {
					$(this).text('0');
				});

				// Удаляем плавающий блок 
				$('#floatingBlock').remove();

				// Очищаем заказ
				for (var key in order.order) {
					delete order.order[key];
				}

			},

			// Смена секции
			changeSection: function() {

				// Завершаем при клике по активной вкладке
				if (~$(this).attr('class').indexOf('active')) return false;

				// Получаем номер текущей секции
				var section = $(this).attr('class').slice( $(this).attr('class').lastIndexOf('-') + 1 );
				
				// Удаляем класс active у секции которая до этого была активной
				$('#sections li.active').removeClass('active');

				// К текущей секции добавляем класс activ
				$(this).addClass('active');

				// Удаляем класс tbody-active ( Скрываем ) у tbody который был активен до этого 
				$('tbody').removeClass('tbody-active');

				// К текущему tbody добавляем класс tbody-active ( Показываем )
				$('.tbody-section-' + section).addClass('tbody-active');
			},

			saveOrder: function() {
				// Отправка заказа на сервер
			}
		};

		return orderObject;

	}());



}