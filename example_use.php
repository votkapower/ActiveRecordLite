<?php 
include_once 'Model.php'; // include the ActiveRecordLite model
/*
 * 1.create new class (If its possible with the name of the table)
 * 2.Extend the ActiveRecordLite model
 * 3.Use parent model construct as shown to enable connection
 * -- Optional --
 * 4. If your class name IS NOT the same as your table name, you can specify the table as shown
 * 5. If your primary key filed IS NOT 'id', you can specify that as well.
 */
class Users extends Model{
	public $table = 'users';
	public $primary_key = 'id';

	function __construct()
	{
		parent::__construct();
	}
}


/*
 * USTAGE: get results 
 * method: find(ID) - searches by the $primary_key you specified
 */
$user = new Users;
echo $user->find(2)->username;

/*
 * USTAGE: get all results 
 * method: all() - returns all records in table
 */
$user = new Users;
echo $user->all();
/*
 * USTAGE: deleteing resultis 
 * method: delete(id?) -  delete an ID that you specify. You can also delte and existing found 'User'
 */
$user = new Users;
echo $user->delete(1); // delete #1 record by id
// ----------------- 
$users = new Users;
$data = $users->find(2);
echo $data->username;
$users->delete(); // delete the found user!
