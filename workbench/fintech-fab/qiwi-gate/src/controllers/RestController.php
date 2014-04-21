<?php

namespace FintechFab\QiwiGate\Controllers;

use Controller;
use FintechFab\QiwiGate\Components\ValidateForFields;
use FintechFab\QiwiGate\Components\WorkWithBill;
use FintechFab\QiwiGate\Models\Bill;
use Input;
use Request;
use Response;
use Validator;


class RestController extends Controller
{

	/**
	 * создание нового счета
	 *
	 * @param int    $provider_id
	 * @param string $bill_id
	 *
	 * @return Response
	 */
	public function store($provider_id, $bill_id)
	{
		$data = Input::all();
		$data['bill_id'] = $bill_id;
		$validator = Validator::make($data, ValidateForFields::rulesForNewBill());
		$messages = $validator->messages()->first();

		if ($messages) {
			$data['error'] = 5;
			$code_response = 400;
			return $this->responseFromGate($data, $code_response);
		}

		if ($data['amount'] < 10) {
			$data['error'] = 241;
			$code_response = 403;
			return $this->responseFromGate($data, $code_response);
		}

		if ($data['amount'] > 5000) {
			$data['error'] = 242;
			$code_response = 403;
			return $this->responseFromGate($data, $code_response);
		}
		$data['status'] = 'waiting';
		if (Bill::where('bill_id', '=', $bill_id)->first() != null) {

			$data['error'] = 215;
			$code_response = 403;

			return $this->responseFromGate($data, $code_response);
		}

		if (WorkWithBill::NewBill($data)) {

			$data['error'] = 0;

			return $this->responseFromGate($data);
		}

		$data['error'] = 13;
		$code_response = 500;
		return $this->responseFromGate($data, $code_response);
	}

	/**
	 * Проверка статуса счета
	 *
	 * @param  int    $provider_id
	 * @param  string $bill_id
	 *
	 * @return Response
	 */
	public function show($provider_id, $bill_id)
	{
		$bill = Bill::where('bill_id', '=', $bill_id)->first();

		if ($bill == null) {
			$data['error'] = 210;
			$code_response = 404;

			return $this->responseFromGate($data, $code_response);
		}
		$data = $bill->toArray();
		$data['error'] = 0;
		foreach ($data as $key => $value) {
			if ($value === null || $key == 'id' || $key == 'merchant_id') {
				unset($bill[$key]);
			}
		}

		return $this->responseFromGate($data);
	}

	/**
	 * Выставление счета
	 *
	 * @param  int    $provider_id
	 * @param  string $bill_id
	 *
	 * @return Response
	 */
	public function update($provider_id, $bill_id)
	{

		if ($this->isCreateBill()) {
			return $this->store($provider_id, $bill_id);
		}

		if ($this->isCancelBill()) {
			dd('update - отмена счета ' . $provider_id . ' / ' . $bill_id);
		}

		return null;

	}

	/**
	 * запрос на отмену счета?
	 *
	 * @return bool
	 */
	private function isCancelBill()
	{

		$method = Request::method();

		if ($method !== 'PATCH') {
			return false;
		}

		$content = file_get_contents('php://input');
		$params = null;
		@parse_str($content, $params);

		return (
			!empty($params['status']) &&
			$params['status'] === 'rejected'
		);

	}

	/**
	 * запрос на создание счета?
	 *
	 * @return bool
	 */
	private function isCreateBill()
	{

		$method = Request::method();

		return ($method === 'PUT');

	}

	private function responseFromGate($data, $code_response = 200)
	{

		$response = array(
			'response' => array(
				'result_code' => $data['error'],
			),
		);
		if (isset($data['status'])) {
			$response['response']['bill'] = $data;
		}

		return Response::json($response, $code_response);
	}

}