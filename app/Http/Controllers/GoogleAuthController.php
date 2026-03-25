<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            if(!empty($googleUser) && !empty($googleUser->email)){
                $user = User::where('email', $googleUser->email)->first();
                if(!empty($user)){
                    $token = $user->createToken('main')->plainTextToken;
                    return redirect('http://localhost:3000/auth/callback?token=' . $token);
                }
            } else {
                header('Location: http://localhost:3000/signin?error=Unable to retrieve email from Google account');
                exit();
            }
            // $user = User::updateOrCreate(
            //     ['google_id' => $googleUser->id],
            //     [
            //         'name' => $googleUser->name,
            //         'email' => $googleUser->email,
            //         // Add other fields you need, like 'avatar' => $googleUser->getAvatar()
            //     ]
            // );

            // Auth::login($user);

            // return redirect('/dashboard');

                // return response()->json([
                //     'user' => $googleUser,
                //     'message' => 'Successfully authenticated with Google'
                // ]);
        } catch (Exception $e) {
            header('Location: http://localhost:3000/signin?error=Unable to retrieve email from Google account');
            exit();
        }
    }
}
