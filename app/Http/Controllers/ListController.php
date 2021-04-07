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
        $key = preg_replace('/\s+/', '', $key);
        if(empty($key)){
            return response()->json(['status' => 'error', 'mesage' => 'Empty key is not allowed'], 400);
        }

        $timestamp = $request->get('timestamp');
        
        if($timestamp == null){
            $data =  KeyValue::where('key', $key)->get();
        } else { 
            $data =  KeyValue_Audit::where('key',$key)->where('updated_at', gmdate("Y-m-d H:i:s", $timestamp))->get();
        }

        foreach($data as $value){
            $value->created_at = $value->created_at->setTimeZone('Asia/Singapore');
            $value->updated_at = $value->updated_at->setTimeZone('Asia/Singapore');
        }

        if(count($data) > 0){
            return response()->json(['status' => 'success', 'mesage' => 'Data retrieval successful', 'results' => $data], 200);
        } else {
            return response()->json(['status' => 'success', 'mesage' => 'No data found with given key : ' . $key, 'results' => ''], 200);
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
                $key = preg_replace('/\s+/', '', $key);
                if(empty($key)){
                    DB::rollback();
                    return response()->json(['status' => 'error', 'mesage' => 'Empty key is not allowed'], 400);
                }
                
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
                        return response()->json(['status' => 'error', 'message' => $e->errorInfo[2]], 500);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data successfully added/updated'], 201);
        }
        catch(Exception $e){
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Failed to save to database. Transaction rolled back.'], 500);
        }
    } 

    public function get_all_records()
    {
        $data = KeyValue::all();

        foreach($data as $value){
            $value->created_at = $value->created_at->setTimeZone('Asia/Singapore');
            $value->updated_at = $value->updated_at->setTimeZone('Asia/Singapore');
        }
        return response()->json(['status' => 'success', 'mesage' => 'Data retrieval successful', 'results' => $data], 200);
    }
}
