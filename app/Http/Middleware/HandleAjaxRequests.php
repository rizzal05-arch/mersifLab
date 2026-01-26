<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleAjaxRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // If this is an AJAX request for admin index, return only the table body
        if ($request->ajax() && $request->is('admin/admins')) {
            $content = $response->getContent();
            
            // Extract the table body from the HTML
            $dom = new \DOMDocument();
            @$dom->loadHTML($content);
            $tbody = $dom->getElementsByTagName('tbody')->item(0);
            
            if ($tbody) {
                return response($dom->saveHTML($tbody), 200, [
                    'Content-Type' => 'text/html'
                ]);
            }
        }
        
        return $response;
    }
}
