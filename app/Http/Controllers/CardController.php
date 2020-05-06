<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Suppport\Facades\DB;

use App\Card;

class CardController extends Controller {

    
    public function index ( Request $request ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $user = $jwtAuth->checkToken( $hash, true );

            $card = Card::where( 'user_id', '=', $user->sub )->get()->load('user');

            if ( count( $card ) == 0 ) {

                $data = array(
                            'card' => $card,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'No tienes direcciones guardadas por el momento'
                        );

            } else {
            
                $data = array(
                            'card' => $card,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'Estas son tus direcciones registradas'
                         );

            }

        } else {
            
            $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'No te haz logeado'
                    );
            
        }

        return response()->json( $data, 200 );

    }


    public function show( Request $request, $id ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $user = $jwtAuth->checkToken( $hash, true );

            if ( $card = Card::where([['card_id', '=', $id], ['user_id', '=', $user->sub]])->first() ) {

                $card = Card::where([['card_id', '=', $id], ['user_id', '=', $user->sub]])->first()->load('user');

                $data = array(
                            'card' => $card,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'Esta es la tarjeta que elegiste'
                        );

            } else {
            
                $data = array(
                            'status' => 'error',
                            'code' => '200',
                            'message' => 'No existe esa tarjeta'
                        );

            }

        } else {
            
            $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'No te has logeado'
                    );
            
        }
        
        return response()->json( $data, 200 );

    }


    public function store( Request $request ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $json = $request->input( 'json', null );
            $params = json_decode( $json );
            $paramsArray = json_decode( $json, true );

            $user = $jwtAuth->checkToken( $hash, true );

            $validate = \Validator::make($paramsArray, [
                            'nombre' => 'required',
                            'numero' => 'required',
                            'tipo' => 'required',
                            'vigencia' => 'required'
                        ]);

            if ( $validate->fails() ){
                return response()->json( $validate->errors(), 400 );
            }

            $card = new Card();

            $card->card_name = $params->nombre;
            $card->card_number = $params->numero;
            $card->card_expired = $params->vigencia;
            $card->card_type = $params->tipo;
            $card->user_id = $user->sub;

            $card->save();

            $data = array(
                        'card' => $card,
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Has agregado una nueva tarjeta'
                    );

        } else {
            
            $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No has iniciado sesión'
                    );

        }

        return response()->json($data, 200);

    }


    public function update ( Request $request, $id ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $json = $request->input( 'json', null );
            $params = json_decode( $json );
            $paramsArray = json_decode( $json, true );

            $user = $jwtAuth->checkToken( $hash, true );

            $validate = \Validator::make($paramsArray, [
                    'nombre' => 'required',
                    'numero' => 'required',
                    'vigencia' => 'required',
                    'tipo' => 'required',
            ]);

            if ( $validate->fails() ){
                return response()->json( $validate->errors(), 400 );
            }

            if ( $card = Card::where( [['card_id', '=', $id], ['user_id', '=', $user->sub]] )->first() ) {

                $card = Card::where([['card_id', '=', $id], ['user_id', '=', $user->sub]])
                                    ->update([
                                        'card_name' => $paramsArray['nombre'],
                                        'card_number' => $paramsArray['numero'],
                                        'card_expired' => $paramsArray['vigencia'],
                                        'card_type' => $paramsArray['tipo']
                                    ]);

                $data = array(
                            'card' => $params,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'Esta es la tarjeta que actualizaste'
                        );

            } else {
            
                $data = array(
                            'status' => 'error',
                            'code' => '200',
                            'message' => 'No existe esa tarjeta'
                        );

            }

        } else {

            $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No has iniciado sesión'
                    );

        }

        return response()->json($data, 200);

    }


    public function destroy ( Request $request, $id ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $user = $jwtAuth->checkToken( $hash, true );

            if ( $card = Card::where([['card_id', '=', $id], ['user_id', '=', $user->sub]])->first() ) {

                $card = Card::where([['card_id', '=', $id], ['user_id', '=', $user->sub]])->first();

                $card->delete();

                $data = array(
                            'card' => $card,
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Se elimino la tarjeta correctamente'
                );

            } else {
            
                $data = array(
                            'status' => 'error',
                            'code' => '200',
                            'message' => 'No existe esa la tarjeta'
                        );

            }

        } else {

            $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No has iniciado sesión'
                    );

        }

        return response()->json($data, 200); 

    }


}
