<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Suppport\Facades\DB;

use App\Address;

class AddressController extends Controller {


    public function index( Request $request ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {
            
            $user = $jwtAuth->checkToken( $hash, true );

            $address = Address::where( 'user_id', '=', $user->sub )->get()->load('user');

            if ( count($address) == 0 ) {

                $data = array(
                            'address' => $address,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'No tienes direcciones guardadas por el momento'
                        );

            } else {
            
                $data = array(
                    'address' => $address,
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

            if ( $address = Address::where([['address_id', '=', $id], ['user_id', '=', $user->sub]])->first() ) {

                $address = Address::where([['address_id', '=', $id], ['user_id', '=', $user->sub]])->first()->load('user');

                $data = array(
                            'address' => $address,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'Esta es la dirección que elegiste'
                        );

            } else {
            
                $data = array(
                            'status' => 'error',
                            'code' => '200',
                            'message' => 'No existe esa dirección'
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
                        'calle' => 'required',
                        'colonia' => 'required',
                        'municipio' => 'required',
                        'ciudad' => 'required',
                        'estado' => 'required',
                        'cp' => 'required',
                        'tel' => 'required',
                        'referencia' => '',
                ]);

                if ( $validate->fails() ){
                    return response()->json( $validate->errors(), 400 );
                }

            
            $address = new Address();
            // var_dump($address); die();
            $address->address_name = $params->nombre;
            $address->address_street = $params->calle;
            $address->address_suburb = $params->colonia;
            $address->address_town = $params->municipio;
            $address->address_city = $params->ciudad;
            $address->address_state = $params->estado;
            $address->address_zip = $params->cp;
            $address->address_tel = $params->tel;
            $address->address_reference = $params->referencia;
            $address->user_id = $user->sub;

            $address->save();

            $data = array(
                        'address' => $address,
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'Has agregado una nueva dirección'
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


    public function update( Request $request, $id ) {

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
                    'calle' => 'required',
                    'colonia' => 'required',
                    'municipio' => 'required',
                    'ciudad' => 'required',
                    'estado' => 'required',
                    'cp' => 'required',
                    'tel' => 'required',
                    'referencia' => '',
            ]);

            if ( $validate->fails() ){
                return response()->json( $validate->errors(), 400 );
            }

            if ( $address = Address::where( [['address_id', '=', $id], ['user_id', '=', $user->sub]] )->first() ) {

                $address = Address::where([['address_id', '=', $id], ['user_id', '=', $user->sub]])
                                    ->update([
                                        'address_name' => $paramsArray['nombre'],
                                        'address_street' => $paramsArray['calle'],
                                        'address_suburb' => $paramsArray['colonia'],
                                        'address_town' => $paramsArray['municipio'],
                                        'address_city' => $paramsArray['ciudad'],
                                        'address_zip' => $paramsArray['cp'],
                                        'address_tel' => $paramsArray['tel'],
                                        'address_reference' => $paramsArray['referencia'],
                                    ]);

                $data = array(
                            'address' => $params,
                            'status' => 'success',
                            'code' => '200',
                            'message' => 'Esta es la dirección que actualizaste'
                        );

            } else {
            
                $data = array(
                            'status' => 'error',
                            'code' => '200',
                            'message' => 'No existe esa dirección'
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

            if ( $address = Address::where([['address_id', '=', $id], ['user_id', '=', $user->sub]])->first() ) {

                $address = Address::where([['address_id', '=', $id], ['user_id', '=', $user->sub]])->first();

                $address->delete();

                $data = array(
                            'address' => $address,
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Se elimino la dirección correctamente'
                );

            } else {
            
                $data = array(
                            'status' => 'error',
                            'code' => '200',
                            'message' => 'No existe esa la dirección'
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
