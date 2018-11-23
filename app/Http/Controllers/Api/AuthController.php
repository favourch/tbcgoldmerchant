<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

      /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $today = date('Y-m-d');

        $payload = auth('api')->payload();

        $user_details = \App\Models\UserDetail::where('user_id', $payload->get('sub'))->first();

        $cashout_count = \App\Models\CashoutTransaction::where('user_id',$payload->get('sub'))
                                            ->whereDate('created_at', $today)
                                            ->count();

        $unilevel_cashout_count = \App\Models\UnilevelTransaction::where('user_id', $payload->get('sub'))
                                                        ->whereDate('created_at', $today)
                                                        ->count();

        $maintenance_pending_count = \App\Models\MaintenanceTransaction::where('user_id', $payload->get('sub'))->where('status', 'pending')->count();

        $level_two_pending_count = \App\Models\MembershipTransaction::where('user_id', $payload->get('sub'))->where('status', 'pending')->where('plan_id', 2)->count();

        $is_level_two = \App\Models\LevelTwoUser::where('user_id', $payload->get('sub'))->count();

        return response()->json([
            'user' => auth('api')->user(),
            'details' => $user_details,
            'cashout_count' => $cashout_count,
            'unilevel_cashout_count' => $unilevel_cashout_count,
            'maintenance_pending_count' => $maintenance_pending_count,
            'level_two_pending_count' => $level_two_pending_count,
            'is_level_two' => $is_level_two ? true : false
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
