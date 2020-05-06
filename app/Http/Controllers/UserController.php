<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\DB;

use App\Mail\VerificationAccount;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Storage;

use App\User;

class UserController extends Controller {
    
    public function register( Request $request ) {
        
        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->nombre)) ? $params->nombre : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $role = 'ROLE_USER';
        $status = false;

        if ( !is_null($email) && !is_null($password) && !is_null($name) ) {

            $user = new User();
            $user->user_email = $email;
            $user->user_name = $name;
            $user->user_role = $role;
            $user->user_status = $status;

            $pwd = hash('sha256', $password);
            $user->user_password = $pwd;

            $isset_user = User::where('user_email', '=', $email)->get()->count();

            if ( $isset_user == 0 ) {

                $user->save();

                $lastId = $user->user_id;
                $codigo="";
                $caracteres="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                $max=strlen($caracteres)-1;

                for ($i = 0; $i < 10; $i ++){
                    $codigo .= $caracteres[rand(0,$max)];
                }

                
                DB::table('codes')->insert([
                    'code_name'=> $codigo,
                    'user_id' => $lastId
                    ]);

                $data = array (
                    'user' => $user,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario registrado correctamente'
                );

                $envio = [
                            'name' => $user->user_name,
                            'email' => $user->user_email,
                            'code' => $codigo
                         ];
                // var_dump($envio['email']);die();

                Mail::to($envio['email'])->queue( new VerificationAccount($envio) );

            } else {
                $data = array (
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Usuario duplicado'
                );
            }

        } else {
            $data = array (
                'status' => 'error',
                'code' => 400,
                'message' => 'Usuario no creado'
            );
        }

        return response()->json($data, 200);

    }

    
    public function login( Request $request ) {
        
        $jwtAuth = new JwtAuth();

        $json = $request->input( 'json', null );
        $params = json_decode( $json );

        $email = ( !is_null( $json ) && isset( $params->email ) ) ? $params->email: null;
        $password = ( !is_null( $json ) && isset( $params->password ) ) ? $params->password: null;
        $getToken = ( !is_null( $json ) && isset( $params->getToken ) ) ? $params->getToken: null;
        
        $pwd = hash( 'sha256', $password );

        if ( !is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false') ) {

            $singup = $jwtAuth->singup( $email, $pwd );
            
        } elseif ( $getToken != null ) {
            
            $singup = $jwtAuth->singup( $email, $pwd, $getToken );

        } else {
            $singup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por post'
            );
        }

        return response()->json($singup, 200);

    }


    public function verifiedAccount ( Request $request ) {

        $json = $request->input( 'json', null );
        $params = json_decode( $json );

        $code = (!is_null($json) && isset($params->code)) ? $params->code : null;

        if ( !is_null($code) ) {

            $codeInfo = DB::table('codes')->where( 'code_name', $code )->first();

            if ( $codeInfo ) {

                $userId = $codeInfo->user_id;
                $userExist = DB::table('users')->where('user_id', $userId)->exists();
                
                if ( $userExist ) {

                    $user = User::find($userId);
                    $user->user_status = true;

                    $user->save();

                    $data = array (
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Has activado tu cuenta Rokstar correctamente'
                    );

                    DB::table( 'codes' )->where( 'code_name', '=', $codeInfo->code_name )->delete();

                } else {

                    $data = array (
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No has creado una cuenta todavía'
                    );

                }
                
            } else {

                $data = array (
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El código que has ingresado es incorrecto'
                );

            }

        } else {

            $data = array (
                'status' => 'error',
                'code' => 400,
                'message' => 'No ingresate ningún código'
            );

        }


        return response()->json($data, 200);

    }

}
