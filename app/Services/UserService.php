<?php


namespace App\Services;


class UserService extends BaseService
{
    
    public function getDataList() {
        return $this->get('/todos');
    }

}