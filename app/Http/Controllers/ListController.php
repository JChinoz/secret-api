<?php

namespace App\Http\Controllers;
use App\Models\KeyValue;
use App\Models\KeyValue_Audit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Request $request, $key)
    {
        $timestamp = $request->get('timestamp');
        
        if($timestamp == null){
            $data =  KeyValue::where('key', $key)->get();
        } else { 
            $data =  KeyValue_Audit::where('key',$key)->where('updated_at', gmdate("Y-m-d H:i:s", $timestamp))->get();
        }

        foreach($data as $value){
            $value->created_at = $value->created_at->setTimezone('Asia/Singapore');
            $value->updated_at = $value->updated_at->setTimezone('Asia/Singapore');
        }

        if(count($data) > 0){
            return response()->json(['status' => 200, 'mesage' => 'Data retrieval successful', 'results' => $data]);
        } else {
            return response()->json(['status' => 200, 'mesage' => 'No data found with given key : ' . $key, 'results' => '']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = request()->json()->all();
        DB::beginTransaction();
        try{
            foreach($data as $key=>$value){
                $keyValuePair = new KeyValue;
                $keyValuePair->key = $key;
                $keyValuePair->value = $value;
                try{
                    $keyValuePair->save();    
                } catch (\Illuminate\Database\QueryException $e) {
                    // Duplicate found
                    if($e->errorInfo[1] == '1062'){
                        $updateKeyValue = KeyValue::where('key', $key);
                        
                        $insertAudit = new KeyValue_Audit;
                        $insertAudit->key = $key;
                        $insertAudit->value = $updateKeyValue->first()->value;
                        $insertAudit->save();
                        // Update new values
                        $updateKeyValue->update(['value' => $value]);
                    } else {
                        DB::rollback();
                        return response()->json(['status' => 500, 'message' => $e->errorInfo[2]], 500);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 200, 'message' => 'Data successfully added/updated'], 200);
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json(['status' => 500, 'message' => 'Failed to save to database. Transaction rolled back.'], 500);
        }
    } 

    public function get_all_records()
    {
        $data = KeyValue::all();
        if(count($data) > 0){
            return response()->json(['status' => 200, 'mesage' => 'Returning all data...', 'results' => $data]);
        } else {
            return response()->json(['status' => 200, 'mesage' => 'No data found in table', 'results' => '']);
        }
    }
}
