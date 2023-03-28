<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use Illuminate\Support\Facades\Storage;


class CompanyController extends Controller
{
    public function listCompanies()
    {
        $items = Company::orderBy('id', 'desc')->get();
        $items->transform(function($item){
            return $item->format();
        });

        return response()->json([
            'listCompanies' => $items
        ]);
    }

    public function addCompany(CompanyRequest $request)
    {
        $file_name = $this->saveImage($request);

        $addCompany = new Company();
        $addCompany->name = $request['name'];
        $addCompany->phone = $request['phone'];
        $addCompany->email = $request['email'];
        $addCompany->address = $request['address'];
        $addCompany->logo = $file_name;
        $addCompany->save();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }


    public function editCompany(CompanyRequest $request)
    {
        //edit
        $editCompany = Company::find($request['id']);
        $editCompany->name = $request['name'];
        $editCompany->phone = $request['phone'];
        $editCompany->email = $request['email'];
        $editCompany->address = $request['address'];

        if (isset($request['logo'])) {
            //Upload File
            $fileName = $this->saveImage($request);

            //Move for old File in folder
            if (isset($editCompany->logo)) {
                $file_path = 'images/Company/Logo/' . $editCompany->logo;
                if (Storage::disk('public')->exists($file_path)) {
                    Storage::disk('public')->delete($file_path);
                }
            }
            $editCompany->logo = $fileName;
        }

        $editCompany->save();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteCompany(CompanyRequest $request)
    {
        $deleteCompany = Company::find($request['id']);
        //Delete File in folder
        if (isset($deleteCompany->logo)) {
            $file_path = 'images/Company/Logo/' . $deleteCompany->logo;
            if (Storage::disk('public')->exists($file_path)) {
                Storage::disk('public')->delete($file_path);
            }
        }
        $deleteCompany->delete();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function saveImage($request)
    {
        if ($request->hasFile('logo')) {
            $destination_path = '/images/Company/Logo';
            $imageFile = $request->file('logo');
            //get just ext
            $extension = $imageFile->getClientOriginalExtension();
            //Filename to storage
            $filename = 'company_logo' . '_' . time() . '.' . $extension;
            Storage::disk('public')->putFileAs($destination_path, $imageFile, $filename);

            return $filename;
        }
    }


}
