<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\Userslist;
use App\Services\Response;
use App\Exceptions\ApplicationException;
use DB;

class SaveController extends Controller
{

    private $userService;

    public function __construct(){
        $this->userService = new UserService();
    }

    /**
     * untuk simpan data package 
    */
    public function save(Request $request){
        
        DB::beginTransaction();
        try {  
            $data           = $this->listData($request);

            if(!empty($data)){
                foreach($data as $row){
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
        $data           = $this->userService->getDataList();
        return $data;
    }




}