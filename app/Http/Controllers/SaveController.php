<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\Userslist;
use App\Services\Response;

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
        $data           = $this->listData($request);

        if(!empty($data)){
            foreach($data as $row){
                $save = Userslist::create([
                    'userId'            => $row->userId,
                    'id'                => $row->id,
                    'title'             => $row->title,
                    'completed'         => $row->completed,
                ]);
            }
        }
        
        return Response::success("Save Data Success");
    }

    /**
     * untuk simpan data package 
    */
    public function listData($request){
        $data           = $this->userService->getDataList();
        return $data;
    }





}