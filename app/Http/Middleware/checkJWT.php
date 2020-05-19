<?php

namespace App\Http\Middleware;
use \Firebase\JWT\JWT;

use Closure;

class checkJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->has('token'))
            return response()->json(['status' => 'Error', 'detail' => 'No token provided']);
        
        $tokenData = $this->verifyJWT($request->token);

        if($tokenData['status'] === 'Ok')
        {
            $encryptedData = $tokenData['data'];

            $decryptData = $this->decryptData($encryptedData);

            if($decryptData['status'] == 'Error')
                return response()->json(['status' => 'Error', 'detail' => 'Unvalid Token'], 403);
            
            $user = $decryptData['data'];
            $request->merge(['user_id'=> $user['id']] );
            $request->merge(['name'=> $user['name']] );
            $request->merge(['email'=> $user['email']] );
            return $next($request);
        }else{
            return response()->json(['status' => 'Error', 'detail' => $tokenData['detail']], 403);
        }       
    }

    private function verifyJWT($token)
    {
        try{
            $key = env('JWT_KEY');
            $data = (array) JWT::decode($token, $key, ['HS256']);

            return ['status' => 'Ok', 'data' => $data];
        }catch(\Exception $e)
        {
            return ['status' => 'Error', 'detail' => $e->getMessage()];
        }
    }

    private function decryptData($data)
    {
        try{
            $returnData = [
                'id' => decrypt($data['id']),
                'name' => decrypt($data['name']),
                'email' => decrypt($data['email'])
            ];

            return ['status' => 'Ok', 'data' => $returnData];
        }catch(\Exception $e)
        {
            return ['status' => 'Error'];
        }
    }
}
