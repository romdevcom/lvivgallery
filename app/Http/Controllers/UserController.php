<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Mail\SendMail;

class UserController extends ObjectController{
	public function index(){
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		if(!isset($user) || empty($user)){
			return redirect(url('user/registration'));
		}
		if(isset($_REQUEST['user-date'])){
            dd($_REQUEST['user-date']);
        }
		$accesses = DB::connection('mysql_users')->table('access')->where('user_id', $user)->get()->toArray();
		$accesses = $this->make_right_access($accesses);
		$allow = false;
		if($user){
			$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
			$user = !empty($user) && is_array($user) ? $user[0] : $user;
			$rights = DB::connection('mysql_users')->table('access')->where([['user_id', $user->id],['status', 'on']])->get()->toArray();
			if(!empty($rights) && count($rights) > 0){
				$date = date('Ymd');
				foreach ($rights as $right){
					$allow = $date >= $right->date_from && $date < $right->date_to;
				}
			}
		}
		return view('user.index', ['user' => $user, 'accesses' => $accesses, 'allow' => $allow]);
	}

	public function registration(){
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		if(isset($user) && !empty($user)){
			return redirect(url('user'));
		}
		return view('user.registration', ['user' => $user]);
	}

	public function admin(){
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		if(empty($user)) return redirect(url('user'));
		$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
		$user = !empty($user) && is_array($user) ? $user[0] : $user;
		if(empty($user->role) || $user->role != 1) return redirect(url('user'));

		$accounts = DB::connection('mysql_users')->table('users')->where('id','!=',$user->id)->get()->toArray();
		foreach ($accounts as $account){
			$access = DB::connection('mysql_users')->table('access')->where('user_id', $account->id)->get()->toArray();
			$access = $this->make_right_access($access);
			$account->access = $access;
		}
		return view('user.admin', ['user' => $user, 'accounts' => $accounts]);
	}

	public function login(){
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		if(isset($user) && !empty($user)){
			return redirect(url('user'));
		}
		$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
		$user = !empty($user) && is_array($user) ? $user[0] : $user;
		return view('user.login', ['user' => $user]);
	}

	public function change(){
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		if(!isset($user) || empty($user)){
			return redirect(url('user/login'));
		}
		$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
		$user = !empty($user) && is_array($user) ? $user[0] : $user;
		return view('user.change', ['user' => $user]);
	}

	public function log_in(){
		$values = !empty($_REQUEST['values']) ? urldecode($_REQUEST['values']) : false;
		if($values){
			$data = $this->get_request_data($values);
			if(!empty($data['user-password']) && !empty($data['user-email'])){
				$id = DB::connection('mysql_users')->table('users')
						->where([['email', $data['user-email']],['password', md5($data['user-password'])]])
						->pluck('id')->toArray();
				return !empty($id) && count($id) > 0 ? $id[0] : 'error';
			}else{
				return 'error';
			}
		}else{
			return 'error';
		}
	}

	public function create(){
		$values = !empty($_REQUEST['values']) ? urldecode($_REQUEST['values']) : false;
		if($values){
			$data = $this->get_request_data($values);
			if($data['user-password'] == $data['user-password-retype']){
				$email = DB::connection('mysql_users')->table('users')
					->where('email', $data['user-email'])
					->pluck('id')->toArray();
				if(count($email) > 0) return 'error email';
				$userId = DB::connection('mysql_users')->table('users')->insertGetId(
					[
						'email' => $data['user-email'],
						'password' => md5($data['user-password']),
						'name' => $data['user-first-name'].' '.$data['user-last-name'],
						'role' => 2,
						'status' => 'off'
					]
				);
				if(!empty($userId)){
					DB::connection('mysql_users')->table('access')->insert(
						[
							'user_id' => $userId,
							'date' => $data['user-date'],
							'description' => $data['user-description'],
							'status' => 'off'
						]
					);
					$message = 'Додано нового користувача, перейдіть за <a href="https://uma.lvivcenter.org/uk/user/admin">посиланням</a> щоб опрацювати заявку.<br>';
					$this->send_mail('m.romaniv@sitegist.com', 'Запит на доступ uma.lvivcenter.org', $message);
					return 'success';
				}else{
					return 'error';
				}
			}else{
				return 'error retype';
			}
		}else{
			return 'error';
		}
	}

	public function request(){
//		$values = !empty($_REQUEST['values']) ? urldecode($_REQUEST['values']) : false;
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : false;
		$description = isset($_REQUEST['description']) ? $_REQUEST['description'] : '';
		if($user){
			if(!empty($date)){
				DB::connection('mysql_users')->table('access')
					->where('user_id', $user)
					->update(array('date' => $date, 'description' => $description));
				$message = 'Користувач <b>'.$user.'</b> залишив запит на доступи до інтерв\'ю <a href="https://uma.lvivcenter.org/uk/user/admin">посиланням</a><br>';
				$this->send_mail('m.romaniv@sitegist.com', 'Запит на доступ uma.lvivcenter.org', $message);
				return 'success';
			}else{
				return 'error';
			}
		}else{
			return 'error';
		}
	}

	public function reset_password(){
		$values = !empty($_REQUEST['values']) ? urldecode($_REQUEST['values']) : false;
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		if($values && $user){
			$data = $this->get_request_data($values);
			$passwordDB = $this->get_user_data($user,'password');
			$passwordOld = md5($data['user-old-password']);
			if($data['user-new-password'] == $data['user-new-password-retype']){
				if($passwordDB == $passwordOld){
					DB::connection('mysql_users')->table('users')
						->where('id', $user)
						->update(['password' => md5($data['user-new-password'])]);
					return 'success';
				}else{
					return 'error old';
				}
			}else{
				return 'error retype';
			}
		}else{
			return 'error';
		}
	}

	public function restore_password_request(){
		$email = !empty($_REQUEST['email']) ? urldecode($_REQUEST['email']) : false;
		if($email){
			$user = DB::connection('mysql_users')->table('users')->where('email', $email)->pluck('id')->toArray();
			if(isset($user) && !empty($user) && count($user) > 0) {
				$key = md5($email . date('mdYhs'));
				DB::connection('mysql_users')->table('users')
					->where('email', $email)
					->update(['restore' => $key]);
				$message = '
					Ви успішно відправили запит на зміну паролю, для того щоб продовжити перейдіть за посиланням: https://uma.lvivcenter.org/uk/user/restore?key='.$key.'
					\nЯкщо ви не відправляли запиту то проігноруйте це повідомлення.					
					';
                Mail::raw($message, function($mail){
                    $mail->from('no-reply@uma.lvivcenter.org', 'UMA');
                    $mail->to('m.romaniv@sitegist.com');
                });

				$mail = $this->send_mail($email, 'Відновлення паролю на uma.lvivcenter.org', $message);
				$mail = $this->send_mail('m.romaniv@sitegist.com', 'Відновлення паролю на uma.lvivcenter.org', $message);
				echo 'success '.$email;
			}else{
                echo 'error email';
			}
		}else{
            echo 'error';
		}
	}

	public function restore_password(){
		$values = !empty($_REQUEST['values']) ? urldecode($_REQUEST['values']) : false;
		if($values){
			$data = $this->get_request_data($values);
			if($data['user-password-retype'] == $data['user-password']) {
				DB::connection('mysql_users')->table('users')
					->where('restore', $data['user-key'])
					->update(array('password' => md5($data['user-password']), 'restore' => ''));
				return 'success';
			}else{
				return 'error retype';
			}
		}else{
			return 'error';
		}
	}

	public function restore(){
		$key = !empty($_REQUEST['key']) ? urldecode($_REQUEST['key']) : false;
		$restore = false;
		if($key){
			$user = DB::connection('mysql_users')->table('users')
				->where('restore', $key)
				->get()->toArray();
			$user = !empty($user) && is_array($user) && count($user) > 0 ? $user[0] : false;
			$restore = $user;
			return view('user.restore', ['user' => $user[0], 'restore' => $restore, 'key' => $key]);
		}else{
			return view('user.restore', ['user' => false, 'restore' => $restore]);
		}
	}

	public function add_access(){
		$values = !empty($_REQUEST['values']) ? urldecode($_REQUEST['values']) : false;
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		$role = $this->get_user_data($user,'role');
		if($role == 1) {
			if ($values) {
				$data = $this->get_request_data($values);
				$dateStart = isset($data['user-date-start']) && !empty($data['user-date-start']) ? str_replace('-', '', $data['user-date-start']) : false;
				$dateEnd = isset($data['user-date-end']) && !empty($data['user-date-end']) ? str_replace('-', '', $data['user-date-end']) : false;
				DB::connection('mysql_users')->table('access')
					->where('user_id', $data['user-id'])
					->update(array(
						'date_from' => $dateStart,
						'date_to' => $dateEnd,
						'admin_id' => $user,
						'status' => 'on'
					));
				DB::connection('mysql_users')->table('users')
					->where('id', $data['user-id'])
					->update(array(
						'status' => 'on'
					));
				return $data['user-id'];
			} else {
				return 'error';
			}
		}else{
			return 'error';
		}
	}

	public function disable_access(){
		$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		$role = $this->get_user_data($user,'role');
		if($id && $role == 1){
			DB::connection('mysql_users')->table('access')
				->where('user_id', $id)
				->update(array(
					'admin_id' => $user,
					'status' => 'off'
				));
			DB::connection('mysql_users')->table('users')
				->where('id', $id)
				->update(array(
					'status' => 'off'
				));
			return $id;
		}else{
			return 'error';
		}
	}

	public function get_request_data($request){
		if(!empty($request)) {
			$data = array();
			$values = explode('&', $request);
			foreach ($values as $value) {
				$item = explode('=', $value);
				$data[$item[0]] = $item[1];
			}
			return $data;
		}else{
			return false;
		}
	}

	public function make_right_access($accesses){
		foreach ($accesses as $access){
			$date = date('Ymd');
			if($access->date_to < $date){
				DB::connection('mysql_users')->table('access')
					->where('id', $access->id)
					->update(['status' => 'off']);
			}
			$access->date_from = !empty($access->date_from) ? $access->date_from[6].$access->date_from[7].'.'.$access->date_from[4].$access->date_from[5].'.'.$access->date_from[0].$access->date_from[1].$access->date_from[2].$access->date_from[3] : false;
			$access->date_to = !empty($access->date_to) ? $access->date_to[6].$access->date_to[7].'.'.$access->date_to[4].$access->date_to[5].'.'.$access->date_to[0].$access->date_to[1].$access->date_to[2].$access->date_to[3] : false;
		}
		return $accesses;
	}

	private function get_user_data($id, $data){
		if(!empty($id) && !empty($data)){
			$value = DB::connection('mysql_users')->table('users')
				->where('id', $id)
				->pluck($data)->toArray()[0];
			return $value;
		}
		return false;
	}

	public function send_mail($to, $subject, $message){

//		$headers = 'From: no-reply@uma.lvivcenter.org' . "\r\n" .
//			'Reply-To: no-reply@uma.lvivcenter.org' . "\r\n" .
//			'X-Mailer: PHP/' . phpversion() . "\r\n";
		mail($to, $subject, $message);
	}
}