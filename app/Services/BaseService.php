<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\Response;

class BaseService
{
    protected function get(String $url, Array $options = []) {
        try {
            $result = Http::withOptions( $options )->get(
                env('API_URI') . $url
            );
            return json_decode($result->body());
        }
        catch (RequestException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 401);   
        } 
        catch (ClientException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 402);   
        } 
        catch (ConnectException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 403);   
        } 
        catch (RequestException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 404);   
        } 
        catch (Exception $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 405);   
        }
    }

    protected function post(String $url, Array $options) {
        // $result = Http::withOptions( $options )->post(
        //     env('API_URI') . $url
        // );
        
        // return $result->body();
        try {
            $result = Http::withOptions( $options )->post(
                env('API_URI') . $url
            );

            return $result->body();
        }
        catch (RequestException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 401);   
        } 
        catch (ClientException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 402);   
        } 
        catch (ConnectException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 403);   
        } 
        catch (RequestException $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 404);   
        } 
        catch (Exception $e) {
            Log::debug($e);
            $response = $e->getResponse();
            if($response === NULL) abort(500, "Cannot get response from backend. Please contact System Admin");
            $jsonObj = json_decode((string) $response->getBody());
            return Response::error($jsonObj, 405);   
        }
    }

    protected function put(String $url, Array $options) {
        $result = Http::withOptions( $options )->put(
            env('API_URI') . $url
        );
        
        return $result->body();
    }
    
}

