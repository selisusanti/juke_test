<?php

/**
 * Created by Ibrahim.
 * Date: Thu, 13 Dec 2018.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User List
 * 
 */
class Userslist extends Model
{
	
	protected $guarded 		= [];
	protected $table 		= 'users_List';
	protected $primaryKey 	= 'id';
	public $timestamps 		= true;

}
