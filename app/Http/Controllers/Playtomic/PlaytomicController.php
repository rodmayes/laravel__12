<?php

namespace App\Http\Controllers\Playtomic;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Playtomic\PlaytomicHttpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PlaytomicController extends Controller
{

    public function login(User $user){
        $service = (new PlaytomicHttpService($user->id));
        $response = $service->login();

        if(isset($response['access_token']) && $response['access_token'] != $user->playtomic_token) {
            $user->playtomic_token = $response['access_token'];
            $user->playtomic_refresh_token = $response['playtomic_refresh_token'] ?: null;
            $user->save();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail']);
    }

    public function updateUser(User $user, Request $request){
        try{
            $user->playtomic_id = $request->playtomic_id;
            $user->save();
            return back()->with('success', $user->name. ' updated successfully.');
        }catch (\Exception $e){
            return back()->with('error', $user->name. ' updated failed. '.$e->getMessage());
        }
    }

    public function refreshToken(User $user){
        try{
        $response = (new PlaytomicHttpService($user->id))->login();
            if($response) {
                $user->playtomic_id = $response['user_id'];
                $user->playtomic_token = $response['access_token'];
                $user->playtomic_refresh_token = $response['refresh_token'];
                $user->save();
                return back()->with('success', $user->name. ' playtomic token refreshed.');
            }
            return back()->with('error', $user->name. ' playtomic token service error failed.');
        }catch (\Exception $e){
            return back()->with('error', $user->name. ' playtomic token refreshed failed. '.$e->getMessage());
        }
    }

    public function storePassword(User $user,Request $request){
        try{
            $user->playtomic_password = Crypt::encrypt($request->playtomic_password);
            $user->save();
            return back()->with('success', $user->name. ' playtomic password saved successfully.');
        }catch (\Exception $e){
            return back()->with('error', $user->name. ' playtomic token refreshed failed. '.$e->getMessage());
        }
    }
}
