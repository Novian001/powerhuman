<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use App\Models\Responsibility;
use Exception;
use Illuminate\Http\Request;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        
        $responsibilityQuery = Responsibility::query();

        if ($id){
            $responsibility = $responsibilityQuery->find($id);

            if ($responsibility){
                return ResponseFormatter::success(
                    $responsibility,
                    'Data responsibility berhasil diambil'
                );
            }    
            return ResponseFormatter::error(
                'Data responsibility tidak ada',
                404
            );
        }

        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        if($name){
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Data list responsibility berhasil diambil'
        );
    }

    public function create(CreateResponsibilityRequest $request){
        try{
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            if(!$responsibility){
                throw new Exception('responsibility gagal ditambahkan');
            }

            return ResponseFormatter::success(
                $responsibility,
                'Data responsibility berhasil ditambahkan'
            );
        } catch (Exception $error){
            return ResponseFormatter::error(
                $error->getMessage(),
                'Data responsibility gagal ditambahkan'
            );
        }
    }

    public function destroy($id){
        try{
            // Get role
            $role = Responsibility::find($id);

            // check if role exists
            if(!$role){
                throw new Exception('Data role tidak ada');
            }

            // Delete role
            $role->delete();

            return ResponseFormatter::success(
                $role,
                'Data role berhasil dihapus'
            );
        } catch (Exception $error){
            return ResponseFormatter::error(
                $error->getMessage(),
                'Data role gagal dihapus'
            );
        }
    }
}
