<?php

namespace App;

use App\Exceptions\APIException;

abstract class ApiClient
{
    protected abstract function getHost();
    protected function call($endpoint, $data = null)
    {
        
        $url = 'http://' . $this->getHost() . '' . $endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        
        if ($data != null) {
            $data_string = json_encode($data);
            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept: application/json'));
        }
        
        $result = curl_exec($ch);
    
        $error = curl_error($ch);
        if ($error){
            throw new APIException('Network error: '.$error, 0, $result);
        }
        curl_close($ch);
        if ($result === '') {
            return '';
        }
        $json = json_decode($result, true);
        if ($json === null) {
            throw new APIException("Invalid json response - " . $result, 0, $result);
        }
        if (isset($json['success']) && !$json['success']) {
            throw new APIException($json['message'], $json['code'], $json['data']);
        }
        return $json;
    }
    
}