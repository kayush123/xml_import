<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;
use App\Models\Contacts;

class XmlController extends Controller
{
    //

    public function show(){
        $files = Files::orderBy('id', 'desc')->get();
        return view('xml_upload', compact('files'));
    }

    public function upload_xml(Request $request){
        if($request->file('xml_file')){
            $file_table = new Files;
            $file = $request->file('xml_file');
            $attch_filename = $file->getClientOriginalName();
            $path = Storage::putFileAs('public', $file, $attch_filename);
            $file_table->file_name = $attch_filename;
            $file_table->file_path = $path;
            $file_table->save();
            return back()->with('success', 'File Uploaded Successfully.');
        } else {
            return back()->with('error', 'Some Error Occured, Please try after sometime!');
        }
    }

    public function import_xml(Request $request){
        try{
            $item_id = $request->item_id;
            $xml = Files::where('id', $item_id)->first();
            if($xml != null){
                $file = asset('storage/'. $xml->file_name);
                $xmlString = file_get_contents($file);
                $xmlObject = simplexml_load_string($xmlString);
                $json = json_encode($xmlObject);
                $contactsArr = json_decode($json, true);
                $contactsArr = $contactsArr['contact'];
                $contacts = [];
                if(count($contactsArr) > 0){
                    foreach($contactsArr as $item){
                        $arr = array('name'=>$item['name'], 'email'=>$item['email'], 'address'=>$item['address'], 'age'=>$item['age'], 'occupation'=>$item['occupation'], 'created_at'=>date('Y-m-d h:i:s'));
                        array_push($contacts, $arr);
                        if(count($contacts) == 100){
                            Contacts::insert($contacts);
                            $contacts = [];
                        }
                    }
                    $xml->status = 1;
                    $xml->save();
                }
                return response()->json(array('status'=>true, 'message'=>'Contacts Imported Successfully.'));
                
            }
        } catch(Exception $e){
            return response()->json(array('status'=>false, 'message'=>$e->getMessage()));
        }
        
    }

    public function delete_xml(Request $request){
        $item_id = $request->item_id;
        $item = Files::where('id', $item_id)->first();
        if($item != null){
            $item->delete();
            return response()->json(array('status'=>true, 'message'=>'File Deleted Successfully.'));
        } else {
            return response()->json(array('status'=>false, 'message'=>'Some Error Occured, Please Try again after sometime!'));
        }
    }
}
