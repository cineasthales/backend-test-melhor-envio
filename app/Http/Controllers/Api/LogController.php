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

            $responseHeaderId = $this->formatObjectAndSave('response_headers', $line->response->headers);
            $responseId = $this->formatObjectAndSave('responses', $line->response, ['response_header_id' => $responseHeaderId]);

            $authenticatedEntityId = $this->formatObjectAndSave('authenticated_entities', $line->authenticated_entity);

            $serviceId = $this->formatObjectAndSave('services', $line->service);

            $routeId = $this->formatObjectAndSave('routes', $line->route, ['service_id' => $serviceId]);

            $latencyId = $this->formatObjectAndSave('latencies', $line->latencies);

            $this->formatObjectAndSave('entries', $line, [
                'request_id' => $requestId,
                'response_id' => $responseId,
                'authenticated_entity_id' => $authenticatedEntityId,
                'service_id' => $serviceId,
                'route_id' => $routeId,
                'latency_id' => $latencyId,
            ]);

            // TODO: querystrings
            // TODO: methods
            // TODO: path
            // TODO: protocols

            dd('stop');

            $this->insertPivot($routeId, 'methods', 'description', 'method_route', 'route_id', 'method_id', $line->route->methods);
            $this->insertPivot($routeId, 'paths', 'description', 'path_route', 'route_id', 'path_id', $line->route->paths);
            $this->insertPivot($routeId, 'protocols', 'description', 'protocol_route', 'route_id', 'protocol_id', $line->route->protocols);

            $arrayLatency = (array) $line->latencies;
            $latencyId = $this->insert('latencies', $arrayLatency);

            $arrayLine = (array) $line;
            $this->insert('entries', [
                'upstream_uri' => $arrayLine['upstream_uri'],
                'client_ip' => $arrayLine['client_ip'],
                'started_at' => $arrayLine['started_at'],
                'request_id' => $requestId,
                'response_id' => $responseId,
                'authenticated_entity_id' => $authenticatedEntityId,
                'route_id' => $routeId,
                'service_id' => $serviceId,
                'latency_id' => $latencyId,
            ]);

            break;
        }
    }

    private function formatObjectAndSave($tableName, $dataObject, $foreignKeys = null)
    {
        $formattedArray = [];
        $toArray = (array) $dataObject;

        foreach ($toArray as $key => $value) {
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

    private function insert($tableName, $data) {
        return DB::table($tableName)->insertGetId($data);
    }





    private function insertPivot($thisTableId, $otherTableName, $otherTableField, $pivotTableName, $thisTableKey, $otherTableKey, $data) {
        $toArray = (array) $data;
        $arrayLength = count($toArray);
        for ($i = 0; $i < $arrayLength; $i++) {
            $savedOtherTable = $this->findOrInsert($otherTableName, $otherTableField, $i, $toArray);
            $this->insert($pivotTableName, [
                $otherTableKey => $savedOtherTable->id,
                $thisTableKey => $thisTableId,
            ]);
        }
    }

    private function findOrInsert($tableName, $tableField, $dataField, $data) {
        $result = DB::table($tableName)->where($tableField, $data[$dataField])->first();

        return $result ? $result->id : $this->insert($tableName, $data);
    }


}