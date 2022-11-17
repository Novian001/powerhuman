<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function fetch(Request $request){

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        
        $companyQuery = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });

        if ($id){
            $company = $companyQuery->find($id);

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
        $companies = $companyQuery;

        if($name){
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Data list company berhasil diambil'
        );
    }

    public function create(CreateCompanyRequest $request){
        try{
            if ($request->hasFile('logo')){
                $path = $request->file('logo')->store('public/logos');
            }
            
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            if(!$company){
                throw new Exception('Gagal membuat company baru');
            }
            // atttach company to user
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // Load users at company
            $company->load('users');
            
            return ResponseFormatter::success(
                $company,
                'Data company berhasil ditambahkan'
            );
        }catch(Exception $e){
            return ResponseFormatter::error(
                $e->getMessage(),
                'Data company gagal ditambahkan'
            );
        }
    }

    public function update(UpdateCompanyRequest $request, $id){
        try{
            // Get company
            $company = Company::find($id);

            // Check if company exists
            if (!$company){
                throw new Exception('Data company tidak ada');
            }

            // Upload logo
            if ($request->hasFile('logo')){
                $path = $request->file('logo')->store('public/logos');
                // $company->logo = $path;
            }

            // $company->name = $request->name;
            // $company->save();

            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo,
            ]);

            return ResponseFormatter::success(
                $company,
                'Data company berhasil diubah'
            );
        }catch(Exception $e){
            return ResponseFormatter::error(
                $e->getMessage(),
                'Data company gagal diubah'
            );
        }
    }
}
