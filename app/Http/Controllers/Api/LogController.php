<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    public function store(Request $request)
    {
        try {
            $text = Storage::disk('logs')->get('logs.txt');
        } catch (\Exception $e) {
            return response()->json(['500' => 'Internal Server Error']);
        }

        $textLines = preg_split('/\r\n|\r|\n/', $text);

        if ($request->offset && $request->length && ($request->offset + $request->length) > 10000) {
            return response()->json(['400' => 'Bad Request']);
        }

        $offset = $request->offset ? $request->offset : 0;
        $length = $request->length ? $request->length : 100;

        $textToProcess = array_slice($textLines, $offset, $length);

        foreach ($textToProcess as $jsonLine) {
            $entry = [];
            $line = json_decode($jsonLine);

            try {
                $requestHeaderId = $this->formatObjectAndSave('request_headers', $line->request->headers);
                $responseHeaderId = $this->formatObjectAndSave('response_headers', $line->response->headers);

                $entryForeignKeys['service_id']  = $this->formatObjectAndSave('services', $line->service);
                $entryForeignKeys['latency_id'] = $this->formatObjectAndSave('latencies', $line->latencies);
                $entryForeignKeys['authenticated_entity_id'] = $this->formatObjectAndSave('authenticated_entities', $line->authenticated_entity->consumer_id);

                $entryForeignKeys['request_id'] = $this->formatObjectAndSave('requests', $line->request, ['request_header_id' => $requestHeaderId]);
                $entryForeignKeys['response_id'] = $this->formatObjectAndSave('responses', $line->response, ['response_header_id' => $responseHeaderId]);
                $entryForeignKeys['route_id'] = $this->formatObjectAndSave('routes', $line->route, ['service_id' => $entryForeignKeys['service_id']]);

                $this->insertPivot($line->request->querystring, 'querystrings', 'querystring_request', 'querystring_id', 'request_id', $entryForeignKeys['request_id']);
                $this->insertPivot($line->route->methods, 'methods', 'method_route', 'method_id', 'route_id', $entryForeignKeys['route_id']);
                $this->insertPivot($line->route->paths, 'paths', 'path_route', 'path_id', 'route_id', $entryForeignKeys['route_id']);
                $this->insertPivot($line->route->protocols, 'protocols', 'protocol_route', 'protocol_id', 'route_id', $entryForeignKeys['route_id']);

                $this->formatObjectAndSave('entries', $line, $entryForeignKeys);

            } catch (\Exception $e) {
                return response()->json(['500' => 'Internal Server Error']);
            }
        }

        return response()->json(['200' => 'OK']);
    }

    private function formatObjectAndSave($tableName, $dataObject, $foreignKeys = null)
    {
        $formattedArray = [];
        $dataArray = (array) $dataObject;
        $hasUUID = false;

        foreach ($dataArray as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                if ($key == 'id') {
                    $formattedArray['uuid'] = $value;
                    $hasUUID = true;
                } else {
                    $formattedKey = strtolower(str_replace('-', '_', $key));
                    $formattedArray[$formattedKey] = $value;
                }
            }
        }

        if ($foreignKeys) {
            $formattedArray = array_merge($formattedArray, $foreignKeys);
        }

        if ($hasUUID) {
            return $this->findOrInsert($tableName, 'uuid', 'uuid', $formattedArray);
        }

        return $this->insert($tableName, $formattedArray);
    }

    private function insertPivot($dataObject, $otherTableName, $pivotTableName, $otherTableKey, $thisTableKey, $thisTableId)
    {
        $dataArray = (array) $dataObject;
        $arrayLength = count($dataArray);
        for ($i = 0; $i < $arrayLength; $i++) {
            $otherTableId = $this->findOrInsert($otherTableName, 'description', $i, $dataArray);
            $this->insert($pivotTableName, [
                $otherTableKey => $otherTableId,
                $thisTableKey => $thisTableId,
            ]);
        }
    }

    private function findOrInsert($tableName, $tableField, $dataKey, $data)
    {
        $result = DB::table($tableName)->where($tableField, $data[$dataKey])->first();
        return $result ? $result->id : $this->insert($tableName, $tableField == 'uuid' ? $data : [$tableField => $data[$dataKey]]);
    }

    private function insert($tableName, $data)
    {
        return DB::table($tableName)->insertGetId($data);
    }
}