<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function me()
    {
        $client = ClientBuilder::create()
            ->setBasicAuthentication('elastic', 'elasticpassword')
            ->setHosts(['http://192.168.56.102:9200'])
            ->build();

        $params = [
            'body' => [
                'query' => '
                SELECT id
                FROM sample1_dev___products_product_type_2
                ORDER BY score() DESC
                '
            ]
        ];

        $response = $client->sql()->query($params);

        $ids = collect($response['rows'])->map(function ($row) {
            return $row[0];
        })->toArray();

        $users = User::whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(", ", $ids) . ')');
        echo $users->toSql();
        exit;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
