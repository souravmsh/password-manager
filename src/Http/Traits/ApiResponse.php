<?php

namespace Souravmsh\PasswordManager\Http\Traits;


trait ApiResponse
{
	public function ajaxSuccess($data=[], $message=null, $redirect=null, $code=200)
	{
		return json_encode([
			'status'   => 'true',
			'code'     => $code,
			'redirect' => $redirect,
			'message'  => $message,
			'data'     => $data,
		]);
	}

	public function ajaxError($data=[], $message=null, $redirect=null, $code=404)
	{
		return json_encode([
			'status'   => 'false',
			'code'     => $code,
			'redirect' => $redirect,
			'message'  => $message,
			'data'     => $data,
		]);
	}

}

