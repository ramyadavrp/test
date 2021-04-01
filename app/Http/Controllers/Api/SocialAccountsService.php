<?php
namespace App\Http\Controllers\Api;
use App\User;
use App\Models\LinkedSocialAccount;
use Laravel\Socialite\Two\User as ProviderUser;

class SocialAccountsService{
    /**
     * Find or create user instance by provider user instance and provider name.
     * 
     * @param ProviderUser $providerUser
     * @param string $provider
     * 
     * @return User
     */
    public function findOrCreate(ProviderUser $providerUser, string $provider): User
    {
        $linkedSocialAccount = \App\LinkedSocialAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();
        if ($linkedSocialAccount) {
            return $linkedSocialAccount->user;
        } else {
            $user = null;
            if ($email = $providerUser->getEmail()) {
                $user = User::where('email', $email)->first();
            }
            if (! $user) {
                $user = User::create([
                    'first_name' => $providerUser->getName(),
                    'last_name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                ]);
            }
            $user->linkedSocialAccounts()->create([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
            ]);
            return $user;
        }
    }
	
	public function socialLogin(Request $request)
    {

    	try {
            $user = [];
            $redirect_to_page = '';            
            $user_exists = [];
            
            $username = $request->user_name;
            if(isset($request->provider_id) && $request->provider_id){
                $user = User::where('provider_id', $request->provider_id)->first();

                if(!empty($user)){
                    $email_check = User::where('email',$request->email)->first();
                    if(empty($email_check)){
                        $user = User::find($user->id);
                        if($user->email==null || $user->email==''){
                            $user->email = $request->email;
                            $user->save();
                            
                        }
                    }
                    
                    $user_exists = clone $user;
                }else{
                    $users = User::where('user_name', $request->user_name)->first();
                    if(!empty($users)){
                        $username =  $this->uniqueUserName($request->user_name);
                    }
                }
            }if(isset($request->email) && $request->email){
                $user = User::where('email', $request->email)->first();
                if(!empty($user)){
                    $user_exists = clone $user;
                }else{
                    $usernme =  $this->uniqueUserName($request->user_name);
                }
            }

            if(empty($user))
    		{
	    		$credential = [
		            'user_name' => isset($username) ? $username : NULL,
		            'email' => isset($request->email) ? $request->email : NULL,
                    'registration_type' => isset($request->registration_type) ? $request->registration_type : NULL,
		            'provider' => $request->provider,
		            'provider_id' => $request->provider_id,
		            'password' => str_random(8),
		            'date_of_birth' => isset($request->date_of_birth) ? $request->date_of_birth : NULL,
                    'name' => isset($request->first_name) ? $request->first_name : NULL,
                    /*'refferal_own_code' => $this->randomId()*/
		        ];
                
		        if ($request->has('profile_picture'))
		        {
		            $credential['profile_picture'] = \Storage::put('avatars', $request->file('profile_picture'));
		        }

			    if ($user)
	            {
	            	//findRoleBySlug
		    	}
		    	else
		    	{
		    		return response()->json([
		                'status_code' => 401,
		                'message' => __('api.general.somethingWrong'),
		                'data' => null
		            ], 401);
		    	}
    		}

            if(strtolower($request->provider) == "facebook" || strtolower($request->provider) == "linkedin" || strtolower($request->provider)=='twitter'){
               //insert code here
            }else{
                //else or elseif of other type of key
            }
            

		    if($user->status)
			{
				//login
                
                //getUserDetailsResponse;

	            return response()->json([
	                'status_code' => 200,
	                'message' => __('api_auth_login'),
	                'data' => [
	                    'access_token' => $tokenResult->accessToken,
	                    'token_type' => 'Bearer',
	                    'expires_at' => Carbon::parse(
	                        $tokenResult->token->expires_at
	                    )->toDateTimeString(),
	                    'user' => $user_details
	                ]
	            ]);
            }
    		else
            {
                //logout;
                return response()->json([
                    'status_code' => 401,
                    'message' => __('api_auth_inactive_account'),
                    'data' => null
                ], 401);
            }
        }
        catch (Exception $e){
            return response()->json([
                'status_code' => 401,
                'message' => $e->getMessage(),
                'data' => null
            ], 401);
        }
    }
}