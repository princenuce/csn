<?php

namespace App\Exceptions;

use Exception;

class SongException extends Exception
{
    protected $message = 'Không tồn tại bai hat này';
    protected $description = '';

    public function __construct($message = '', $description = ''){
    	if($message){
    		$this->message = $message;
    	}

    	if($description){
    		$this->description = $description;
    	}
    }

    public function report() 
    {

    }

    public function render($request) {
        $params = [
            'message' => $this->message,
            'description' => $this->description
        ];

        $view = \App\Library\Services\Functions::$template . '.errors.song';

        return response()->view($view, $params);
    }
}
