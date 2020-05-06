<?php
namespace APP\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;


class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = 'Ryusei0510376-Blackmore-Yaya';
    }

    public function singup( $email, $password, $getToken = null ){

        $user = User::where(
                    array (
                        'user_email' => $email,
                        'user_password' => $password
                    ))->first();

        $altaUser = User::where(
                    array(
                        'user_email' => $email,
                        'user_password' => $password,
                        'user_status' => true
                ))->first();

        $signup = false;

        $alta = false;

        if ( is_object($user) ) {
            $signup = true;
        }

        if ( is_object($altaUser) ) {
            $alta = true;
        }

        if ( $signup && $alta ) {

            $token = array(
                'sub' => $user->user_id,
                'email' => $user->user_email,
                'name' => $user->user_name,
                'role' => $user->user_role,
                'iat' => time(),
                'exp' => time() + ( 7 * 24 * 60 *60 )
            );

            $jwt = JWT::encode( $token, $this->key, 'HS256' );
            $decoded = JWT::decode( $jwt, $this->key, array('HS256') );

            if ( is_null($getToken) ) {

                return $jwt;

            } else {

                return $decoded;

            }

        } elseif( $signup && !$alta ) {

            return array (
                    'status' => 'error',
                    'message' => 'No has activado tu cuenta Rokstar, revisa tu correo por favor.'
                );

        } else {

            return array (
                    'status' => 'error',
                    'message' => 'Login ha fallado'
                );

        }

    }


    public function checkToken( $jwt, $getIdentity = false ) {

        $auth = false;

        try {

            $decoded = JWT::decode( $jwt, $this->key, array('HS256') );

        } catch ( \UnexpectedValueException $e ) {
            $auth = false; 
        } catch ( \DomainException $e ) {
            $auth = false; 
        }

        if ( isset($decoded) && is_object($decoded) && isset($decoded->sub) ) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ( $getIdentity ) {
            return $decoded;
        }

        return $auth;

    }


}

?>