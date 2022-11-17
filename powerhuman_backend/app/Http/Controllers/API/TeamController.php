<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function fetch(Request $request){
    
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        
        $teamQuery = Team::query();

        if ($id){
                $company = $teamQuery->find($id);

            if ($company){
                return ResponseFormatter::success(
                    $company,
                    'Data company berhasil diambil'
                );

                return ResponseFormatter::error(
                    'Data company tidak ada',
                    404
                );
            }    
        }
        $teams = $teamQuery->where('company_id', $request->company_id);

        if($name){
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Data list company berhasil diambil'
        );
    }

    public function create(CreateTeamRequest $request){
        try {
            // dd ($request->all());
            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Create team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            if (!$team) {
                throw new Exception('Team not created');
            }

            return ResponseFormatter::success($team, 'Team created');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id){
        // dd($request->all());
        try{
            $team = Team::find($id);
            
            if (!$team){
                throw new Exception('Data team tidak ada');
            }

            if ($request->hasFile('icon')){
                $path = $request->file('icon')->store('public/icons');
            }

            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $team,
                'Data team berhasil diubah'
            );
        } catch (Exception $error){
            return ResponseFormatter::error(
                $error->getMessage(),
                'Data team gagal diubah'
            );
        }
    }

    public function destroy($id){
        try{
            // Get team
            $team = Team::find($id);
            
            // Check if team exists
            if (!$team){
                throw new Exception('Data team tidak ada');
            }

            // Delete team
            $team->delete();

            return ResponseFormatter::success(
                $team,
                'Data team berhasil dihapus'
            );
        } catch (Exception $error){
            return ResponseFormatter::error(
                $error->getMessage(),
                'Data team gagal dihapus'
            );
        }
    }
}
