<?php

namespace App\Http\Controllers\API\Call_Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user->load('role');
        $currentToken = $request->bearerToken();
        $data = $user->getApiUserData($currentToken);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }
}
