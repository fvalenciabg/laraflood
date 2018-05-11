<?php

namespace Vincendev\Laraflood;

use Carbon\Carbon;

class Laraflood
{

    /**
     * @param $identity
     * @return bool
     */
    public function checkOnly( $identity = 'ip', $action = 'default', $maxAttempts = 5 )
    {
        if(!is_numeric($maxAttempts)) return;
        if( $identity == 'ip' ) $identity = $this->getrealip();

        $key = 'lf:'.$identity.':'.$action;

        if( !\Cache::has( $key ) )
            return true;
        $count = \Cache::get( $key )['attempts'];
        if( !$count || !is_numeric($count) )
            return true;

        # Check actions count per `cache_time` minutes
        if( $count < $maxAttempts )
            return true;

        return false;
    }

    public function check( $identity = 'ip', $action = 'default', $maxAttempts = 5, $minutes = 5 ){

        if( !is_numeric($maxAttempts)|| !is_numeric($minutes) ) return;
        # add attempt
        $this->addAttempt($identity, $action, $minutes);
        # check
        return $this->checkOnly($identity, $action, $maxAttempts);

    }

    /**
     * pushes identityify key to cache
     *
     * @param     $identity
     * @param     $action
     * @param int $minutes
     */
    public function addAttempt( $identity = 'ip', $action = 'default', $minutes = 5 )
    {
        if( $identity == 'ip' ) $identity = $this->getrealip();
        if( !is_numeric($minutes) ) return;

        $data = array('action' => $action, 'attempts' => 1, 'expiration' => Carbon::now()->addMinutes($minutes));

        $key = 'lf:'.$identity.':'.$action;

        if( \Cache::has($key) ) {
            $c = \Cache::get($key);
            $updated_data = array('action' => $action, 'attempts' => $c['attempts'] + 1, 'expiration' => $c['expiration']); 
            \Cache::put($key, $updated_data, $c['expiration']);
        } else {
            \Cache::put($key, $data, $minutes);
        }
    }

    /**
     * Gets the time left from the cache
     *
     * @param     $identity
     * @param     $action
     */
    public function timeLeft( $identity = 'ip', $action = 'default')
    {
        if( $identity == 'ip' ) $identity = $this->getrealip();
        $key = 'lf:'.$identity.':'.$action;

        if( !\Cache::has( $key ) )
            return;

        $now = Carbon::now();
        $expiration = Carbon::parse(\Cache::get( $key )['expiration']);

        if($expiration->diffInSeconds($now) > 60 ){
            if($expiration->diffInMinutes($now) > 60 ){
                return $expiration->diffInHours($now) . ' hours';
            }else{
                return $expiration->diffInMinutes($now) . ' minutes';
            }
        }else{
            return $expiration->diffInSeconds($now) . ' seconds';
        }



    }








    public function get($identity = 'ip', $action = 'default'){
        if( $identity == 'ip' ) $identity = $this->getrealip();
        $key = 'lf:'.$identity.':'.$action;
        return \Cache::get($key);

    }

    
    
    private function getrealip()
    {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
        return $ip;
    }


}