<?php
namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TruFlix{

    public function getSessionUser(){

        $user = null;
        if(Auth::guard('zq')->check()){
           $user = Auth::guard('zq')->user();
        }elseif(Auth::guard('admin')->check()){
           $user = Auth::guard('admin')->user();
        }elseif(Auth::guard('partner')->check()){
            $user = Auth::guard('partner')->user();
        }elseif(Auth::guard('web')->check()){
           $user = Auth::guard('web')->user();
        }

        return $user;
    }

    public function getCurrentGuard(){
        $user = $this->getSessionUser();
        if($user->isZq()){
            //Super-admin guard...
            return 'zq';
        }elseif($user->isAdmin()){
            //Admin guard...
            return 'admin';
        }elseif($user->isPartner()){
            //Partner guard...
            return 'partner';
        }

        return 'web';
    }

    public function getLogoutUrl(){
        $user = $this->getSessionUser();
        if($user->isZq()){
            //Super-admin guard...
            return route('zq.logout');
        }elseif($user->isAdmin()){
            //Admin guard...
            return route('admin.logout');
        }elseif($user->isPartner()){
            //Partner guard...
            return route('partner.logout');
        }
        //Todo:Add additional  logouts for other guards here before that add function to check user guard in model..

        return route('web.logout');
    }

    public function ratings(){
        $ratings = [];
        for ($i = 1; $i <= 10; $i++):
            $ratings[] = $i;
            if ($i < 10):
                for ($j = 1; $j < 10; $j++):
                    $ratings[] = $i.'.'.$j;
                endfor;
            endif;
        endfor;

        return $ratings;
    }

    public function ratedList(){
        $ratedList = [
            'G - General Audience',
            'PG - Parental Guidance',
            'PG13 - Parental Guidance',
            'R - Restricted',
            'MA - Mature'
        ];

        return $ratedList;
    }


    public function moviesResponse($movie){
        dd($movie);
    }
}
