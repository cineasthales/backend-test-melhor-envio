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

        foreach ($textLines as $jsonLine) {
            $line = json_decode($jsonLine);

            $requestHeaderId = $this->formatObjectAndSave('request_headers', $line->request->headers);
            $requestId = $this->formatObjectAndSave('requests', $line->request, ['request_header_id' => $requestHeaderId]);

            $this->insertPivot($line->request->querystring, 'querystrings', 'description', 'querystring_request', 'querystring_id', 'request_id', $requestId);

            $responseHeaderId = $this->formatObjectAndSave('response_headers', $line->response->headers);
            $responseId = $this->formatObjectAndSave('responses', $line->response, ['response_header_id' => $responseHeaderId]);

            $authenticatedEntityId = $this->formatObjectAndSave('authenticated_entities', $line->authenticated_entity->consumer_id);

            $serviceId = $this->formatObjectAndSave('services', $line->service);

            $routeId = $this->formatObjectAndSave('routes', $line->route, ['service_id' => $serviceId]);

            $this->insertPivot($line->route->methods, 'methods', 'description', 'method_route', 'method_id', 'route_id', $routeId);
            $this->insertPivot($line->route->paths, 'paths', 'description', 'path_route', 'path_id', 'route_id', $routeId);
            $this->insertPivot($line->route->protocols, 'protocols', 'description', 'protocol_route', 'protocol_id', 'route_id', $routeId);

            $latencyId = $this->formatObjectAndSave('latencies', $line->latencies);

            $this->formatObjectAndSave('entries', $line, [
                'request_id' => $requestId,
                'response_id' => $responseId,
                'authenticated_entity_id' => $authenticatedEntityId,
                'service_id' => $serviceId,
                'route_id' => $routeId,
                'latency_id' => $latencyId,
            ]);

            dd('foi');

            break;
        }
    }

    private function formatObjectAndSave($tableName, $dataObject, $foreignKeys = null)
    {
        $formattedArray = [];
        $dataArray = (array) $dataObject;

        foreach ($dataArray as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $key = $key == 'id' ? 'uuid' : strtolower(str_replace('-', '_', $key));
                $formattedArray[$key] = $value; 
            }
        }

        if ($foreignKeys) {
            $formattedArray = array_merge($formattedArray, $foreignKeys);
        }

        return $this->insert($tableName, $formattedArray);
    }

    private function insertPivot($dataObject, $otherTableName, $otherTableField, $pivotTableName, $otherTableKey, $thisTableKey, $thisTableId) {
        $dataArray = (array) $dataObject;
        $arrayLength = count($dataArray);
        for ($i = 0; $i < $arrayLength; $i++) {
            $otherTableId = $this->findOrInsert($otherTableName, $otherTableField, $i, $dataArray);
            $this->insert($pivotTableName, [
                $otherTableKey => $otherTableId,
                $thisTableKey => $thisTableId,
            ]);
        }
    }

    private function findOrInsert($tableName, $tableField, $dataKey, $data) {
        $result = DB::table($tableName)->where($tableField, $data[$dataKey])->first();
        return $result ? $result->id : $this->insert($tableName, [$tableField => $data[$dataKey]]);
    }

    private function insert($tableName, $data) {
        return DB::table($tableName)->insertGetId($data);
    }
}