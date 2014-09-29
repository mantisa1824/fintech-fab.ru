<?php

/**
 * @var array $menu
 * @var array $sections
 */

// Если $end_dinner не определена по умолчанию false;
$end_dinner = empty($end_dinner) ? false : $end_dinner;

// Пока у нас один юзер
$user_id = 5;

if(!$end_dinner):

	// Если $end_dinner === false отрисовываем табличку для заказа обеда?>

	<div class="modal fade" id="modal-order">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title">Заказ</h4>
	      </div>
	      <div class="modal-body">
	       	
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Оичстить</button>
	        <button type="button" class="btn btn-primary">Оформить заказ</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->



		

	<h3>Сделать заказ</h3>
	
	<ul id="sections">
		<?php 

		$i = 0;
		foreach($sections as $section): ?>
			<?php if(!$i):

				$section_id = $section->id;?>
				<li class="section-id-<?=$section->id?> active"><?= $section->title ?></li>
			<?php else:?>
				<li class="section-id-<?=$section->id?>"><?= $section->title ?></li>
			<?php endif;?>
		<?php 
		$i = 1;
		endforeach ?>

	</ul>

	<table class="table table-hover">

		<thead>
			<tr>
				<th class="first">Название блюда</th>
				<th>Описание Блюда</th>
				<th>Цена</th>
				<th>Заказ</th>
			</tr>
		</thead>

		<?php $prev = null?>

	<?php foreach($menu as $food):?>
		
			<?php if($food->section_id !== $prev):?>
				<?php if($food->section_id === $section_id):?>
					<tbody class="tbody-section-<?=$food->section_id?> tbody-active">
				<?php else:?>
					<tbody class="tbody-section-<?=$food->section_id?>">
				<?php endif?>
			<?php endif?>

					<tr class="this-food-id-<?=$food->id?>">
						<td class="first"><?=$food->title?></td>
						<td><?=$food->description?></td>
						<td class="cost"><?=$food->price?></td>
						<td>
							<span class="less glyphicon glyphicon-minus-sign"></span>&nbsp;
							<span class="quantity">0</span>&nbsp;
							<span class="more glyphicon glyphicon-plus-sign"></span>
						</td>
					</tr>

		<?php $prev = $food->section_id;?>
		
	<?php endforeach;?>

	</table>

	<input type="hidden" id="user-id" value="<?=$user_id?>" />

<?php else:

	// Если $end_dinner === true значит заказ обедов окончен ?>

	<div id="end-dinner">
		<h3>Недоступно</h3>
		<div>Заказ обеда можно сделать в период с 8.00 до 16.00 .<br/>
			Текущие время : <span id="show-time"></span>
		</div>

		<div style="margin-top:7px;">
			<button type="button" class="btn btn-danger" onclick="window.history.back()">Назад</button>
		</div>
	</div>

<?php endif;?>