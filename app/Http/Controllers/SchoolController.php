<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
class SchoolController extends Controller
{
    function index(){
        return view('school.index');
    }

    function upload(Request $request){
        // return $request->all();
       
       
        $the_file = $request->file('uploaded_file');

        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 1, $row_limit );
            $column_range = range( 'F', $column_limit );
            $startcount = 0;
            $school_list = [];
            $i = 0;
            $temp_district = NULL;
            $temp_division = NULL;
            $temp_thana = NULL;

            foreach ( $row_range as $key => $row ) {
                $extraData = [];
                // $default = $sheet->getCell( 'C' . $row )->getValue() ?? 'Nothing';

                if(!empty($sheet->getCell( 'C' . $row )->getValue()))
                {
                    $combined_colC = explode(" ", ($sheet->getCell( 'C' . $row )->getValue()));
                    $temp_division = $combined_colC[0] ?? NULL;
                    $temp_district = explode("\n",$combined_colC[1] )[0] ?? NULL;
                    $temp_thana = explode("\n",$combined_colC[1] )[1]?? NULL . ' ' . ($combined_colC[2] ?? NULL) ?? null;

                }

                elseif($sheet->getCell( 'A' . $row )->getValue()=='Sl' || $sheet->getCell( 'E' . $row )->getValue()=='Name' ){
                    continue;
                }


                // DB::connection('saif-vai')->table('education_boards')->where('name', 'LIKE', "%{$extraData['division']}%");

                else{
                    $school_list[] = [
                        'key' => $key,
                        'division' => $temp_division,
                        'district' => $temp_district,
                        'thana' => $temp_thana,
                        'name' => $sheet->getCell( 'E' . $row )->getValue(),
                        'eiin' => $sheet->getCell( 'B' . $row )->getValue(),
                        'mobile' => $sheet->getCell( 'AC' . $row )->getValue(),
                        'village_road' => $sheet->getCell( 'W' . $row )->getValue(),
                        // 'data' => $row
                    ];
                    
                }
            }

            
            
            // return $school_list;
            // die();
            
            
            
            foreach($school_list as $item)
            {               
                // School::insert($item);
                
                $school = new School;
                $school->division = $item['division'];
                $school->district = $item['district'];
                $school->thana = $item['thana']; 
                $school->name = $item['name'];
                $school->eiin = $item['eiin'];
                $school->mobile = $item['mobile'];
                $school->village_road =$item['village_road'];
                $school->save();
            }
            
            return $school_list;

        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
    }
}
