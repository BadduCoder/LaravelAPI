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

//----------------------->If Boss Exists and Worker needs to be assigned to him, it's not possible
                if(Boss::where('bname',$bname)->exists())
                {
                    if(Workers::where('wname',$wname)->exists())
                    {
                      $work = $wname;
                      $count = 0;
                      while(true)
                      {
                          if(Workers::where('wname',$work)->exists())
                          {
                              $work = Workers::where('wname',$work)->get()[0]->boss->bname;
                              $count++;
                              if($work == $bname)
                              {
                                  $returns = 'Relation Already exists! Levels between existing relations :';
                                  $returns .= (string)$count;
                                  return response()->json(['success'=>$returns]);
                              }
                          }
                          else {
                              $returne = 'Not Possible because ';
                              return response()->json(['error'=>$returne]);
                          }

                      }
                    }
                    else
                    {
                        $return = 'Not Possible because ';
                        $return .= $bname;
                        $return .= ' is already boss of ';
                        $return .= (string)Boss::where('bname',$bname)->get()[0]->worker->wname;
                        return response()->json(['error'=>$return]);
                    }
                }
                //If the worker already has a boss
                $ww = $wname;
                if(Workers::where('wname',$wname)->exists())
                {
                    $count =0;
                    $returne = 'Not Possible because ';
                    $returne .= $wname;
                    $returne .= ' already has boss who are: ';
                    while(true)
                    {
                        if(Workers::where('wname',$ww)->exists())
                        {
                          $count++;
                          $ww = Workers::where('wname',$ww)->get()[0]->boss->bname;
                          $returne .= (string)$ww;
                          $returne .= ",";
                          if($ww == $bname)
                          {
                              $returns = 'Relation Already exists!';
                              $returns .= (string)$count;
                              return response()->json(['success'=>$returns]);
                          }
                        }
                        else
                        {
                            $returne = substr($returne, 0 , -5);
                            $returne .= " try making ".$bname." boss of ".Boss::where('bname','TOP')->get()[0]->worker->wname;
                            return response()->json(['error'=>$returne]);
                        }

                    }

                }
                //Now no boss exist just adding
                $BObj = new Boss;
                $BObj->bname = $bname;
                $BObj->save();
                $id = Boss::where('bname',$bname)->get()[0]->id;
                $WObj = new Workers;
                $WObj->wname = $wname;
                $WObj->boss_id = $id;
                $WObj->save();
                $extra = ' Successfully Created Entries!';
                if($superboss=Boss::where('bname','TOP')->exists())
                  return $this->show($extra);
                else
                  return response()->json(['success'=>$extra]);
            }

      }

      public function show($extra = '')
      {
        if($superboss=Boss::where('bname','TOP')->exists())
        {
          $superboss=Boss::where('bname','TOP')->get()[0]->bname;
          if(isset($extra))
          $final = $extra;
          $final .= "Now the Hierarchy is : ";
          $final .= (string)$superboss;
          $i = 0;
          while(true)
          {
              if(Boss::where('bname',$superboss)->exists())
              {
                $superboss = Boss::where('bname',$superboss)->get()[0]->worker->wname;
                $final.="->";
                $final.=(string)$superboss;
                $i++;
              }
              else {
                break;
              }

          }
          return response()->json(['success'=>$final]);
        }
        else {
          return response()->json(['error'=>'Please define SuperBoss using TOP!']);
        }
      }
}
