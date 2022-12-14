<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TestService;
use App\Models\Userslist;
use App\Services\Response;
use App\Exceptions\ApplicationException;
use DB;

class SaveController extends Controller
{

    private $testService;

    public function __construct(){
        $this->testService = new TestService();
    }

    /**
     * untuk simpan data package 
    */
    public function save(Request $request){
        

        DB::beginTransaction();
        try {  
            $data           = $this->listData($request);

            if(!empty($data->data)){
                foreach($data->data as $row){
                    $save = Userslist::create([
                        'userId'            => $row->userId,
                        'title'             => $row->title,
                        'completed'         => $row->completed,
                    ]);
                }
            }

            DB::commit();
            return Response::success("Save Data Success");
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error($e, 500);
        }
        
    }

    /**
     * untuk simpan data package 
    */
    public function listData($request){
        $data           = $this->testService->coba();
        return $data;
    }




}