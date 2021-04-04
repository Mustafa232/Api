<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);
        
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        
        
        $user->save();

        
        
        
        
        return response()->json([
            'success' => "true",
            'message' => 'Registration Successful. Please verify and log in to your account.'
        ], 201);
        
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        
        $credentials = request(['email', 'password']);
        
        if (!Auth::attempt($credentials))
            return response()->json(['message' => 'Unauthorized'], 401);
            
        $user = $request->user();
        
        
        
        $tokenResult = $user->createToken('Personal Access Token');
        
        return $this->loginSuccess($tokenResult, $user);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    

    protected function loginSuccess($tokenResult, $user)
    {
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(100);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                
            ]
        ]);
    }
    
    public function checkVerification(Request $request) {
        
        $request->validate(['email' => 'required|string|email' ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            
            return response()->json([
                'success' => false,
                'message' => 'We can not find a user with that e-mail address'
            ], 404);
                
        } else {
            
            if($user->email_verified_at == null) {
                
                return response()->json([
                    'success' => true,
                    'message' => 'Non Verified'
                ], 200);
                
            } else {
                
                return response()->json([
                    'success' => true,
                    'message' => 'Verified'
                ], 200);
                
            }
            
        }
        
    }
    
    
    
    
    public function sendCodeForgetPassword(Request $request) {
        
        $request->validate(['email' => 'required|string|email' ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            
            return response()->json([
                'success' => false,
                'message' => 'We can not find a user with that e-mail address'
            ], 404);
                
        } 
            return response()->json(['success' => true,'message' => 'True' ], 200);
        
        
    }
    
    
    
}
