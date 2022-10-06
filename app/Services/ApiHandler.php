<?php

namespace App\Services;

use Session;
use App\Constants\Constant;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Redirect;

class ApiHandler
{
    /**
     * request
     * make api call with guzzle
     * @param $method : HTTP request methods eg: GET , POST , PUT etc.
     * @param $url : request url after base_uri
     * @param array $params : request params
     * @param $multipart : set true if need multipart
     * @param array $headers : custom headers for request
     * @param $useAccessToken : set true if need Authorization header
     * @return BaseResponse
     */
    public static function request($method, $url,$params = [],$headers = [],$multipart = false, $useAccessToken = true , $isFile = false){

        // get base url from environtment
        $client = new Client(['base_uri' => env("API_BASE_URL")]);
        $method = strtoupper($method);
        $options = [];
        // add content of headers to option headers
        $options["headers"] = $headers;
        // get request time out from environtment
        $options["timeout"] = env('API_REQUEST_TIMEOUT');
        
        if($useAccessToken){
            if(!empty(Session::get("access_token"))){
                $options["headers"]["Authorization"] = "Bearer ".Session::get("access_token");
            }else{
                // action when acess token doesn't exist
                // remove access token from session
                ApiHandler::removeAccessToken();
            }
        }
        
        // prepare method
        if(!empty($params)){
            switch ($method) {
                case "GET":
                    $options["headers"] += [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ];

                    $options["query"] = $params;
                    break;
                default:
                    if($multipart) {
                        
                        $multipart_params = [];
                        foreach ($params as $key => $param) {
                            if (!empty($param)) {
                                if (is_object($param)) {
                                    $multipart_params[] = [
                                        'filename' => $param->getClientOriginalName(),
                                        'name'     => $key,
                                        'contents' => file_get_contents( $param->getPathName() ),
                                    ];
                                }else{
                                    $multipart_params[] = [
                                        "name" => $key,
                                        "contents" => $param
                                    ];
                                }
                            } 
                        }

                        $options["multipart"] = $multipart_params;

                    }else {
                        $options["headers"] += [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ];

                        $options["json"] = $params;
                    }
            }
        }
        try {

            if ($isFile) {
                $options["stream"] = true;
                $response = $client->request(strtoupper($method), $url, $options);
                if($response->getBody() === NULL) abort($response->statusCode, $response->reasonPhrase.". Please contact System Admin");

                return \Response::make($response->getBody()->getContents(), 200, $response->getHeaders());
            }else{
                $response = $client->request(strtoupper($method), $url, $options);
                if($response->getBody() === NULL) abort($response->statusCode, $response->reasonPhrase.". Please contact System Admin");
                
                $jsonObj = json_decode((string) $response->getBody());
                // karena response dari api beda
                return new BaseResponse($jsonObj ? true:false, $jsonObj->code ?? null,$jsonObj->message ?? null , $jsonObj ?? null);
            }
        }
        catch (RequestException $e) {
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            // $statusCode            = json_decode((string) $response->getStatusCode());

            if($useAccessToken){
                if($jsonObj !== NULL){

                    if ($jsonObj->code == 401) {
                        ApiHandler::removeAccessToken("Your session is expired, please login again.");
                    }else if ($jsonObj->code == 403) {
                        // if user is unauthorized (403) move to dashboard or login if not logged in
                        Session::put("access_token",null);
                        Session::flush();
                        Session::flash('error', $jsonObj->message);
                        Redirect::away('/')->send();
                    }

                }
                // if ($statusCode == 401) {
                //     ApiHandler::removeAccessToken("Your session is expired, please login again.");
                // }else if ($statusCode == 403) {
                //     // if user is unauthorized (403) move to dashboard or login if not logged in
                //     Session::put("access_token",null);
                //     Session::flush();
                //     Session::flash('error', $jsonObj->message);
                //     Redirect::away('/')->send();
                // }
            }
            
            return new BaseResponse($jsonObj->status ?? false,$jsonObj->code ?? null,$jsonObj->message ?? null , $jsonObj->data ?? null);
        }
        
    }

    /**
     * requestWithoutAccessToken
     * Same as request method, with accesstoken set to false as default
     * @param $method : HTTP request methods eg: GET , POST , PUT etc.
     * @param $url : request url after base_uri
     * @param array $params : request params
     * @param $multipart : set true if need multipart
     * @param array $headers : custom headers for request
     * @return BaseResponse
     */
    public static function requestWithoutAccessToken($method, $url,$params = [],$headers = [],$multipart = false){
        return ApiHandler::request($method, $url, $params, $headers, $multipart, false);
    }

    /**
     * requestMultipart
     * Same as request method, with multipart set to true as default
     * @param $method : HTTP request methods eg: GET , POST , PUT etc.
     * @param $url : request url after base_uri
     * @param array $params : request params
     * @param $multipart : set true if need multipart
     * @param array $headers : custom headers for request
     * @return BaseResponse
     */
    public static function requestMultipart($method, $url,$params = [],$headers = [],$useAccessToken = true){
        return ApiHandler::request($method, $url, $params, $headers, true, $useAccessToken);
    }
    
    /**
     * downloadFile
     * Same as request method, with multipart set to true as default
     * @param $method : HTTP request methods eg: GET , POST , PUT etc.
     * @param $url : request url after base_uri
     * @param array $params : request params
     * @param $multipart : set true if need multipart
     * @param array $headers : custom headers for request
     * @return BaseResponse
     */
    public static function downloadFile($method, $url,$params = [],$headers = [],$multipart = false, $useAccessToken = true){
        return ApiHandler::request($method, $url, $params, $headers, $multipart, $useAccessToken , true);
    }

    public static function removeAccessToken($errMsg = null){
        // remove access token from session
        // flush, save then regenerate to clean old session data
        if(isset($errMsg)){
            Session::flash('error', $errMsg);
        }
        Session::put("access_token",null);
        Redirect::away('/')->send();
        
    }

    //Tendang ke Dashboard
    public static function inCaseForbidden($errMsg = null){
        Session::flash('error', $errMsg);
        return  redirect('/dashboard');
    }
    
}

/**
 * BaseResponse
 * Base response class for api call
 * @param Bool $status : request status 
 * @param Int $code : api response code
 * @param Object $message : message from api from
 * @param Object $data : data response from api
 */
final class BaseResponse
{
    public $status;
    public $code;
    public $message;
    public $data;

    public function __construct($status,$code,$message = "",$data = [])
    {
        $this->status = $status;
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }
} 