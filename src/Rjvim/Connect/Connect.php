<?php namespace Rjvim\Connect;

use Config;
use Request;
use Response;
use Redirect;
use Google_Client;
use Session;

class Connect {


	protected $sentinel;

	/**
	 * Constructor for Connect Library
	 */

	public function __construct()
	{
		$this->sentinel = \App::make('sentinel');
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function handle($provider)
	{
		$req = Request::instance();

		if($req->has('code'))
		{
			$userData = $provider->takeCare();
		}
		else
		{
			return $provider->authenticate();
		}

		$user = $this->findUser(array('type' => 'login','value' => $userData['email']));

		if($user['found'])
		{
			//If a user is found - check is he has a oauthaccount - update or create accordingly
			$user = $user['user'];
		}
		else
		{
			//If a user is not found, create a user and a oauth account for him
			$user = $this->createUser($userData,true);
		}

		$provider->updateOAuthAccount($user,$userData);

		//Then log in a user
		$this->sentinel->login($user,true);

		if(Config::get('rjvim.connect.ajax'))
		{
			return Response::json('success',200);
		}

	    if (Session::has('redirect')) {
	        $referrer = Session::get('redirect');
	        Session::forget('redirect');
	        return redirect()->to($referrer);
	    }
                
		return redirect()->intended(Config::get('rjvim.connect.route'));
	}

	/**
	 * Authenticate using Google
	 * 
	 * @param  string $client [description]
	 * @param  string $scope  [description]
	 * @param  string $state  [description]
	 * @return [type]         [description]
	 */
	public function google($client = 'default',$scope = 'default', $state = 'default')
	{
		//To be implemented
		if($state == 'youtube_access')
		{
			$provider = new Providers\Youtube($client,$scope,$state);
		}
		else
		{
			$provider = new Providers\Google($client,$scope,$state);
		}

		return $this->handle($provider);

	}

	/**
	 * To be implemented
	 *
	 * @return void
	 * @author 
	 **/
	public function github($client = 'default',$scope = 'default')
	{
		$provider = new Providers\Github($client,$scope);

		return $this->handle($provider);

	}
	
	/**
	 * To be implemented
	 *
	 * @return void
	 * @author 
	 **/
	public function facebook($client = 'default',$scope = 'default')
	{
		$provider = new Providers\Facebook($client,$scope);

		return $this->handle($provider);

	}


	/**
	 * Find user using sentinel methods
	 *
	 * @return void
	 * @author 
	 **/
	public function findUser($criteria)
	{
		if($criteria['type'] == 'id')
		{
			$user = $this->sentinel->findUserById($criteria['value']);
		}

		if($criteria['type'] == 'login')
		{
			$user = $this->sentinel->findByCredentials(['login' => $criteria['value']]);
		}

		if($user)
		{
			$result['found'] = true;
			$result['user'] = $user;
		}
		else
		{
			$result['found'] = false;
		}

		return $result;

	}

	/**
	 * Create a user
	 *
	 * @return Create user
	 * @author Rajiv Seelam
	 **/
	public function createUser($data,$activate = false)
	{
		if(isset($data['birthday']))
		{
			$data['birthday'] = \Carbon::createFromTimestamp(strtotime($data['birthday']));
		}

		$password = isset($data['password']) ? $data['password'] : str_random(10);

		$user = $this->sentinel->registerAndActivate(array(
			        'email'       => $data['email'],
			        'name'        => $data['name'],
			        'password'    => $password
			    ));

		if(in_array($data['gender'], ['male','female', 'others']))
		{
			$user->gender = $data['gender'];
		}

		if(isset($data['birthday']) && $data['birthday'])
		{
			$user->birthday = $data['birthday'];
		}

		if(isset($data['description']))
		{
			$user->description = strip_tags($data['description']);
		}

		if(isset($data['image']))
		{
			$user->photo = $data['image'];
		}

		$user->save();

		return $user;

	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function getAuthUrl($provider,$client = 'default',$scope = 'default', $state = 'default')
	{
		$client = new Providers\Google($client,$scope,$state,true);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function google_client($oauthAccount, $client = 'default',$scope = 'default', $state = 'default')
	{
		$provider = new Providers\Google($client,$scope,$state,true);

		$gClient = $provider->prepareClient($client,$scope,$state,true);

		$gClient->setAccessToken(unserialize($oauthAccount->signature));

		if($gClient->isAccessTokenExpired())
		{
			$gClient->refreshToken($oauthAccount->refresh_token);

			$response = $gClient->getAccessToken();

			$actual_response = $response;

			$response = json_decode($response);

			$oauthAccount->access_token = $response->access_token;

			if(isset($response->refresh_token))
			{
				$oauthAccount->refresh_token = $response->refresh_token;
			}

			if(isset($response->created))
			{
				$oauthAccount->created = $response->created;
			}

			$oauthAccount->expires_in = $response->expires_in;

			$oauthAccount->signature = serialize($actual_response);

			$oauthAccount->save();
		}

		return $gClient;

	}
}