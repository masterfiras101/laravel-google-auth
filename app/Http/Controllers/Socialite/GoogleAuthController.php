<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;


class GoogleAuthController extends Controller
{

    /**
     * Redirects the user to Google for authentication using Socialite.
     * 
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getGoogleAuth()
    {
        return Socialite::driver('google')->redirect();

    }



    /**
     * Handles the Google authentication callback. 
     * It retrieves the user's data from Google, logs in the user if they exist, 
     * or creates a new user and logs them in. In case of an error, it redirects to the welcome page with an error message
     * 
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function getGoogleAuthCallback()
    {


        // this for test
        // $googleUserData = Socialite::driver('google')->user();
        // if ($googleUserData) {
        //     dd([
        //         'id' => $googleUserData->getId(),
        //         'name' => $googleUserData->getName(),
        //         'email' => $googleUserData->getEmail(),
        //         'avatar' => $googleUserData->getAvatar(),
        //     ]);
        // } else {
        //     dd('data not found');
        // }

        try {
            $googleUserData = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUserData->id)->orWhere('email', $googleUserData->email)->first();

            if ($user) {
                Auth::login($user);
                return redirect()->route('auth.google.success');
            } else {
                $newUser = User::create([
                    'name' => $googleUserData->name,
                    'email' => $googleUserData->email,
                    'password' => Hash::make('password@1234'),
                    'google_id' => $googleUserData->id,
                    'avatar' => $googleUserData->avatar,
                ]);

                // Log in the new user
                Auth::login($newUser);
                return redirect()->route('auth.google.success');
            }
        } catch (Exception $e) {
            return redirect()->route('welcome')->withErrors(['error' => 'Failed to authenticate with Google.']);
        }
    }

    /**
     * Displays the dashboard view after a successful Google authentication
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getGoogleAuthSuccess()
    {
        return view('dashboard');
    }
}
