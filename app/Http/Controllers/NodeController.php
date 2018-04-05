<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Workers;
use App\Boss;

class NodeController extends Controller
{
      public function add(Request $request)
      {
            //Validating the inputs
            $validate = Validator::make(
                array(
                  'bname' => $request->input('bname'),
                  'wname' => $request->input('wname')
                ),
                array(
                  'bname' => 'required',
                  'wname' => 'required'
                )
            );

            if($validate->fails())
                return response()->json($validator->errors(),422);

            else
            {
                //Verification Passed, Now Checking Conditions:
                $bname = $request->input('bname');
                $wname = $request->input('wname');

                //If Boss Exists and Worker needs to be assigned to him, it's not possible
                if(Boss::where('bname',$bname)->exists() && !Workers::where('wname',$wname)->exists())
                      return response()->json(['error'=>'Not Possible!']);

                //Checking Whether Boss Already Exits
                if(Boss::where('bname',$bname)->exists())
                {
                      $newboss = $bname ;
                      while(true)
                      {
                            if(Boss::where('bname',$newboss)->exists())
                            {
                              if(Boss::where('bname',$newboss)->get()[0]->worker->wname == $wname )
                                  return response()->json(['success'=>'Already Exists!']);
                            }
                            if(Boss::where('bname',$newboss)->exists())
                                $newboss = Boss::where('bname',$newboss)->get()[0]->worker->wname;
                            else
                                break;
                      }
                      if(Workers::where('wname',$wname)->exists() || Boss::where('bname',$wname)->exists())
                            return response()->json(['error'=>'Not Possible!']);
                }

                //Checking Whether new Worker is Someone's Boss
                if(Workers::where('wname',$wname)->exists())
                {
                      $neww = $wname ;
                      while(true)
                      {
                            if(Workers::where('wname',$neww)->exists())
                              if(Workers::where('wname',$neww)->get()[0]->boss->bname == $wname )
                                  return response()->json(['error'=>'Not Possible!']);
                            if(Workers::where('wname',$neww)->exists())
                                $neww = Workers::where('wname',$neww)->get()[0]->boss->bname;
                            else
                                break;
                      }
                      if(Workers::where('wname',$wname)->exists() || Boss::where('bname',$bname)->exists())
                            return response()->json(['error'=>'Not Possible!']);
                }

                //If Boss is Worker and Worker is Boss after above filtering
                if(Workers::where('wname',$bname)->exists() && Boss::where('bname',$wname)->exists())
                    return response()->json(['error'=>'Not Possible!']);

                //Now no boss exist just adding
                $BObj = new Boss;
                $BObj->bname = $bname;
                $BObj->save();
                $id = Boss::where('bname',$bname)->get()[0]->id;
                $WObj = new Workers;
                $WObj->wname = $wname;
                $WObj->boss_id = $id;
                $WObj->save();

                return response()->json(['success'=>'Successfully Created Entries!']);
            }
      }
}
