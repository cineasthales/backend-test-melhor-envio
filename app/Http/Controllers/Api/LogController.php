<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $text = Storage::disk('logs')->get('logs.txt');

        $textLines = preg_split('/\r\n|\r|\n/', $text);

        foreach ($textLines as $line) {
            $lineObject = json_decode($line);

            $this->insertInto('entries', [
                'upstream_uri' => $lineObject->upstream_uri,
                'client_ip' => $lineObject->client_ip,
                'started_at' => $lineObject->started_at,
                'request_id' => 0,
                'response_id' => 0,
                'authenticated_entity_id' => 0,
                'route_id' => 0,
                'service_id' => 0,
                'latency_id' => 0,
            ]);

            //$requestObject = $lineObject->request;
            //unset($requestObject->querystring);
            //unset($requestObject->headers);
            //$requestObject->header_id = 0;
            //DB::table('requests')->insert((array) $requestObject);

            
            break;
        }
    }

    private function insertInto($table, $data) {
        DB::table($table)->insert($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
}