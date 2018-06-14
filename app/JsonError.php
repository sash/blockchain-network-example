<?php

namespace App;

class JsonError implements \JsonSerializable
{
    private $message;
    private $code;
    public $data;
    
    /**
     * JsonError constructor.
     * @param $message
     * @param $code
     */
    public function __construct($message, $code=0)
    {
        $this->message = $message;
        $this->code = $code;
    }
    
    public static function fromException(\Throwable $throwable){
        $res = new self($throwable->getMessage(), $throwable->getCode());
        $res->data = $throwable->getTraceAsString();
        return $res;
    }
    
    public static function message($message, $code=0){
        return new self($message, $code);
    }
    
    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     * @deprecated We agreed to use REST response calls
     */
    public function jsonSerialize()
    {
        return ['success' => false, 'message'=>$this->message, 'code' => $this->code, 'data' => $this->data];
    }
    
    public function response($status=500){
        return response(json_encode($this), $status);
    }
}