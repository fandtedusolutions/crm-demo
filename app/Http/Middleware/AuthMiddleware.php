<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\AuthHelper;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in using our custom AuthHelper
        if (!AuthHelper::isLoggedIn()) {
            return redirect()->route('login')
                ->with('message', 'Session expired, Login Again!');
        }

        // Add user data to request attributes for easy access without polluting query parameters
        $request->attributes->add([
            'user_id' => AuthHelper::getUserId(),
            'user_name' => AuthHelper::getUserName(),
            'role_id' => AuthHelper::getRoleId(),
            'role_title' => AuthHelper::getRoleTitle(),
            'user_designation' => AuthHelper::getUserDesignation(),
        ]);

        // Merge into request input on non-GET requests for backward compatibility with form handlers
        if (!$request->isMethod('GET')) {
            $request->merge([
                'user_id' => AuthHelper::getUserId(),
                'user_name' => AuthHelper::getUserName(),
                'role_id' => AuthHelper::getRoleId(),
                'role_title' => AuthHelper::getRoleTitle(),
                'user_designation' => AuthHelper::getUserDesignation(),
            ]);
        }

        // Remove auth keys from query parameters so paginators, exports, and URL builders never leak them into URLs
        $request->query->remove('user_id');
        $request->query->remove('user_name');
        $request->query->remove('role_id');
        $request->query->remove('role_title');
        $request->query->remove('user_designation');

        return $next($request);
    }
}
