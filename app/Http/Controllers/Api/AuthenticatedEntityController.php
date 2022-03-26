<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthenticatedEntityController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $contents = DB::table('requests')
            ->select('authenticated_entities.uuid', 'requests.method', 'requests.uri',
                'requests.url', 'requests.size', 'request_headers.accept',
                'request_headers.host', 'request_headers.user_agent')
            ->join('request_headers', 'requests.request_header_id', '=', 'request_headers.id')
            ->join('entries', 'entries.request_id', '=', 'requests.id')
            ->join('authenticated_entities', 'entries.authenticated_entity_id', '=', 'authenticated_entities.id')
            ->orderBy('authenticated_entities.uuid')
            ->get();

        $report = [];
        $report[0] = 'uuid,method,uri,url,size,accept,host,user_agent' . PHP_EOL;

        foreach ($contents as $content) {
            $report[] = implode(',', (array) $content) . PHP_EOL;
        }

        Storage::disk('local')->put('report.csv', $report);
    }
}
