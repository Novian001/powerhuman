<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function fetch(Request $request){
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibilities = $request->input('with_responsibilities', false);
        
        $roleQuery = Role::withCount('employees');

        if ($id){
            $role = $roleQuery->find($id);

            if ($role){
                return ResponseFormatter::success(
                    $role,
                    'Data role berhasil diambil'
                );

                return ResponseFormatter::error(
                    'Data role tidak ada',
                    404
                );
            }    
        }
        $roles = $roleQuery->where('company_id', $request->company_id);
        
        if($name){
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if($with_responsibilities){
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Data list role berhasil diambil'
        );
    }

    public function create(CreateRoleRequest $request){
        try{
            $role = Role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            if(!$role){
                throw new Exception('role gagal ditambahkan');
            }
            
            return ResponseFormatter::success(
                $role,
                'Data role berhasil ditambahkan'
            );
        } catch (Exception $error){
            return ResponseFormatter::error(
                $error->getMessage(),
                'Data role gagal ditambahkan'
            );
        }
    }

    public function update(UpdateRoleRequest $request, $id){
        try{
            $role = Role::find($id);

            if ($role){
                $role->update([
                    'name' => $request->name,
                    'company_id' => $request->company_id,
                ]);

                return ResponseFormatter::success(
                    $role,
                    'Data role berhasil diubah'
                );
            } else {
                return ResponseFormatter::error(
                    'Data role tidak ada',
                    404
                );
            }
        } catch (Exception $error){
            return ResponseFormatter::error(
                $error->getMessage(),
                'Data role gagal diubah'
            );
        }
    }

    public function destroy($id){
        try{
            // Get role
            $role = Role::find($id);

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
