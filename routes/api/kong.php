<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;

require('./class/utils.php');

$app->group('/kong', function (RouteCollectorProxy $group) {

    $getKongConfig = function(){
        $kong = $this->get('configs')["app_configs"]["kong"];
        if(!isset($kong)) throw new Exception("Kong config not found");

        $use = $kong["use_https"] ? 'https' : 'http';
        return $kong[$use]["host"].":".$kong[$use]["admin"];
    };

    $group->get('/services', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        $url = empty($params['id']) ? $getKongConfig()."/services/" : $getKongConfig()."/services/".$params['id'];
        $resp = $utils->apicall($url,"get");

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });

    $group->post('/services', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        if(empty($params['id'])) unset($params['id']);
        if(empty($params['name'])) unset($params['name']);
        if(empty($params['port'])){
            unset($params['port']);
        }else{
            $params['port']= (int)$params['port'];
            if($params['port']==0) unset($params['port']);
        }
        if(empty($params['protocol'])) unset($params['protocol']);
        if(empty($params['path'])) unset($params['path']);

        $update=false;
        if(!empty($params['id'])) $update=true;
        if(!$update&&empty($params['host'])) throw new Exception("Parameter 'host' not found");

        $url = $update ? $getKongConfig()."/services/".$params['id'] : $getKongConfig()."/services/";
        $method = $update ? 'patch' : 'post';
        $resp = $utils->apicall($url,$method,$params);

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });

    $group->get('/routes', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        $url = empty($params['id']) ? $getKongConfig()."/routes/" : $getKongConfig()."/routes/".$params['id'];
        $resp = $utils->apicall($url,"get");

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });

    $group->post('/routes', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        if(empty($params['id'])) unset($params['id']);
        if(empty($params['hosts'])) $params['hosts']=null;
        if(empty($params['methods'])) $params['methods']=null;
        if(empty($params['paths'])) $params['paths']=null;
        if(empty($params['protocols'])) $params['protocols']=null;
        if(empty($params['name'])) $params['name']=null;

        $update=false;
        if(!empty($params['id'])) $update=true;
        if(!$update&&empty($params['service']['id'])) throw new Exception("'Id service' not found");
        if(empty($params['protocols'])) throw new Exception("Parameter 'protocols' not found");
        if(!$update&&empty($params['paths'])) throw new Exception("Parameter 'paths' not found");

        $url = $update ? $getKongConfig()."/routes/".$params['id'] : $getKongConfig()."/routes/";
        $method = $update ? 'patch' : 'post';
        $resp = $utils->apicall($url,$method,$params);

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });

    $group->delete('/routes', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        if(empty($params['id'])) throw new Exception("Parameter 'id' not found");

        $url = $getKongConfig()."/routes/".$params['id'];
        $resp = $utils->apicall($url,'delete');

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });

    $group->get('/plugins', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        $url = empty($params['id']) ? $getKongConfig()."/plugins/" : $getKongConfig()."/plugins/".$params['id'];
        $resp = $utils->apicall($url,"get");

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });

    $group->post('/plugins', function (Request $request, Response $response, array $args) use($getKongConfig) {
        $utils = new Utils();
        $params = $utils->getParams($request);

        if(empty($params['id'])) unset($params['id']);
        if(empty($params['name'])) unset($params['name']);
        if(empty($params['config']['token_expiration'])){
            unset($params['config']['token_expiration']);
        }else{
            $params['config']['token_expiration']= (int)$params['config']['token_expiration'];
            if($params['config']['token_expiration']==0) unset($params['config']['token_expiration']);
        }
        if(empty($params['protocols'])) unset($params['protocols']);
        if(empty($params['config']['provision_key'])) unset($params['config']['provision_key']);
        if(empty($params['config']['scopes'])) unset($params['config']['scopes']);

        $update=false;
        if(!empty($params['id'])) $update=true;
        if(!$update&&empty($params['name'])) throw new Exception("Parameter 'Plugin type' not found");
        if(!$update&&empty($params['service']['id'])&&empty($params['route']['id'])) throw new Exception("'Id service' or 'Id route' not found");

        $url = $update ? $getKongConfig()."/plugins/".$params['id'] : $getKongConfig()."/plugins/";
        $method = $update ? 'patch' : 'post';
        $resp = $utils->apicall($url,$method,$params);

        $response->getBody()->write($resp);
        return $response->withStatus(200)
                        ->withHeader("Content-Type", "application/json");
    });
});
