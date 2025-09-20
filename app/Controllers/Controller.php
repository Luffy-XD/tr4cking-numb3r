<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Validator;
use App\Core\Session;

abstract class Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        Session::start();
        $this->checkCsrf();
    }

    protected function validate(array $rules): array
    {
        $data = $this->request->all();
        $trimmed = [];
        foreach ($data as $key => $value) {
            $trimmed[$key] = is_string($value) ? trim($value) : $value;
        }
        $validator = new Validator($trimmed, $rules);
        if ($validator->fails()) {
            Session::put('_errors', $validator->errors());
            Session::put('_old', $trimmed);
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
        return $trimmed;
    }

    protected function success(string $message, string $redirectPath): void
    {
        Session::flash('success', $message);
        redirect($redirectPath);
    }

    protected function error(string $message, string $redirectPath): void
    {
        Session::flash('error', $message);
        redirect($redirectPath);
    }

    private function checkCsrf(): void
    {
        if (in_array($this->request->method(), ['POST', 'PUT', 'DELETE'], true)) {
            $token = $this->request->input('_token');
            if (!$token || $token !== Session::get('_token')) {
                http_response_code(419);
                echo 'Token CSRF tidak valid';
                exit;
            }
        }
    }
}
