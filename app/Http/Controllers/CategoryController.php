<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Suppport\Facades\DB;

use App\Category;

class CategoryController extends Controller {


    public function index () {

        $category = new Category();

        $category = Category::all();

        if ( count( $category ) == 0 ) {

            $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'No hay categorías'
                    );

        } else {

            $data = array(
                        'categories' => $category,
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Estas son todas las categorías'
                    );

        }

        return response()->json( $data, 200 );

    }


    public function show ( $id ) {

        $category = new Category();

        $category = Category::find( $id );

        if ( !$category ) {

            $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Esa categoría no existe'
                    );

        } else {

            $data = array(
                        'categories' => $category,
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Estas son todas las categorías'
                    );

        }

        return response()->json( $data, 200 );

    }


    public function store ( Request $request ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $user = $jwtAuth->checkToken( $hash, true );

            if ( $user->role == 'ADMIN_USER') {

                $json = $request->input( 'json', null );
                $params = json_decode( $json );
                $paramsArray = json_decode( $json, true );

                $user = $jwtAuth->checkToken( $hash, true );

                $validate = \Validator::make( $paramsArray, [
                                'nombre' => 'required'
                            ]);

                if ( $validate->fails() ){
                    return response()->json( $validate->errors(), 400 );
                }

                $checkCategory = new Category();

                if ( $checkCategory = Category::where( 'category_name', '=', $params->nombre )->first() ) {
                    
                    $data = array(
                            'category' => $checkCategory,
                            'status' => 'error',
                            'code' => 400,
                            'message' => 'Esa categoría ya existe'
                        );

                } else {

                    $category = new Category();

                    $category->category_name = $params->nombre;

                    $category->save();

                    $data = array(
                                'category' => $category,
                                'status' => 'success',
                                'code' => 200,
                                'message' => 'Has agregado una categoría nueva'
                            );

                }

            } else {

                $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No tienes acceso a esto'
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


    public function update ( Request $request, $id ) {

        $hash = $request->header( 'Authorization', null );

        $jwtAuth = new jwtAuth();
        $checkToken = $jwtAuth->checkToken( $hash );

        if ( $checkToken ) {

            $user = $jwtAuth->checkToken( $hash, true );

            if ( $user->role == 'ADMIN_USER') {

                $json = $request->input( 'json', null );
                $params = json_decode( $json );
                $paramsArray = json_decode( $json, true );

                $user = $jwtAuth->checkToken( $hash, true );

                $validate = \Validator::make( $paramsArray, [
                                'nombre' => 'required'
                            ]);

                if ( $validate->fails() ){
                    return response()->json( $validate->errors(), 400 );
                }

                if ( $category = Category::find( $id ) ) {

                    $category = Category::find( $id )
                                ->update( array('category_name' => $paramsArray['nombre']) );

                    $data = array(
                                'category' => $params,
                                'status' => 'success',
                                'code' => '200',
                                'message' => 'Esta es la categoría que actualizaste'
                            );

                } else {
            
                    $data = array(
                                'status' => 'error',
                                'code' => '200',
                                'message' => 'No existe esa categoría'
                            );

                }

            } else {

                $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No tienes acceso a esto'
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

            if ( $user->role == 'ADMIN_USER') {

                if ( $category = Category::find( $id ) ) {

                    // $category = Category::find( $id );
                    $category->delete();

                    $data = array(
                                'category' => $category,
                                'status' => 'success',
                                'code' => '200',
                                'message' => 'Esta es la categoría que eliminaste'
                            );

                } else {
            
                    $data = array(
                                'status' => 'error',
                                'code' => '200',
                                'message' => 'No existe esa categoría'
                            );

                }

            } else {

                $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'No tienes acceso a esto'
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
