<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //mengambil paameter 'email' dan 'password' dari request
        $credentials = $request->only('email', 'password');

        try {
            //kondisi bila email dan password tidak terdaftar
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credential Tidak Sesuai'], 400);
            }
        } 
        //kondisi bila terjadi kesalahan pada library JWT 
        catch (JWTException $e) {
            return response()->json(['error' => 'Generate Token Gagal'], 500);
        }

        //token yang didapatkan bila login berhasil
        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        //validasi yang dilakukan dengan library validator untuk melihat apakah inputan pengguna sesuai
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'gender' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //kondisi bila validasi tidak terpenuhi
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        //menambahkan data user di dalam database
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'gender' => $request->get('gender'),
            'password' => Hash::make($request->get('password')),
        ]);

        //update token di dalam tabel user
        $token = JWTAuth::fromUser($user);

        //hasil yang keluar saat register berhasil
        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {
            //kondisi bila user dari token tidak terdaftar
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        }
        //kondisi bila token kadaluarsa
        catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } 
        //kondisi bila terjadi kesalahan pada token
        catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        }
        //kondisi bila ada kesalahan pada library JWT
        catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        //hasil dari proses validasi pengguna
        return response()->json(compact('user'));
    }
}
