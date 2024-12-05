<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class ProxyController extends Controller
{
    /**
     * Handle the proxy request to fetch web page content.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function proxy(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            return response()->json(['error' => 'URL query parameter is required'], 400);
        }

        try {
            // Fetch the content of the page using Browsershot
            $content = Browsershot::url($url)->bodyHtml(); // Get only the HTML content of the page

            return response($content, 200)
                ->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching the requested URL: ' . $e->getMessage()], 500);
        }
    }
}
