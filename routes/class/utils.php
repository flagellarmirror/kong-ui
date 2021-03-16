<?php

class Utils {
    public function getParams($request){
        if(strtoupper($request->getMethod())=='GET'){
            $params = array_change_key_case($request->getQueryParams(), CASE_LOWER);
        }else{
            $content = $request->getHeaders();
            $content = explode(";",$content['Content-Type'][0]);
            $json=false;
            for($i=0;$i<sizeof($content);$i++){
                if($content[$i]=='application/json'){
                    $json=true;
                    break;
                }
            }
            if($json){
                $body= file_get_contents("php://input");
                $params = array_change_key_case(json_decode($body, true), CASE_LOWER);
            }else{
                $params = array_change_key_case($request->getParsedBody(), CASE_LOWER);
            }
        }
        return $params;
    }

    public function apicall($url,$method,$params=null,$headers=null){
        $method=strtoupper($method);
        if(empty($headers)) $headers=array("Content-Type:application/json");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if($method!='GET'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if(!empty($params)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        $httpcode = curl_getinfo($ch);
        if($httpcode['http_code']!=200&&$httpcode['http_code']!=201&&$httpcode['http_code']!=204) {
            $output=json_decode($output,true);
            throw new Exception($output['message']);
        }
        return $output;
    }
}
