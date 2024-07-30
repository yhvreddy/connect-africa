<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterSettings;
use App\Repositories\MasterSettingsRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\HttpResponses;
use App\Http\Traits\TruFlix;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SettingsRequest;
use App\Http\Requests\SettingsUpdateRequest;
use App\Http\Resources\v1\MoviesResource;

class SettingsController extends Controller
{
    use HttpResponses, TruFlix;

    protected $masterSettingsRepository;

    public function __construct(
        MasterSettingsRepository $masterSettingsRepository
    ) {
        $this->masterSettingsRepository = $masterSettingsRepository;
    }
	

    public function createOrEditSettings() {
        $siteDetails = $this->masterSettingsRepository->getDetailsByType('site_details')->first();
        if($siteDetails){
            $socialMediaDetails = $this->masterSettingsRepository->getDetailsByType('social_media')->select('id', 'title', 'url')->get();
            return view('zq.settings.edit_site_details', compact('siteDetails', 'socialMediaDetails'));
        }
        return view('zq.settings.add_site_details');
    }


    public function saveSettings(SettingsRequest $request){
        try {
            $siteDetails = $this->masterSettingsRepository->getDetailsByType('site_details')->first();
            if($siteDetails){
                return redirect()->back()->with('failed', 'Already saved settings');
            }

            $response = DB::transaction(function () use ($request) {

                $data = [
                    'title' => $request->name,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'copyrights' => $request->copy_rights,
                    'type' => 'site_details'
                ];

                $settings = $this->masterSettingsRepository->create($data);
                if ($settings) {
                    if(isset($request->logo) && !empty($request->logo)){
                        $logo = $this->uploadWithName($request, 'logo', 'uploads/settings');
                        $settings->logo = $logo['path'] ?? null;
                        $settings->save();
                    }

                    if(isset($request->fav_icon) && !empty($request->fav_icon)){
                        $favIcon = $this->uploadWithName($request, 'fav_icon', 'uploads/settings');
                        $settings->fav_icon = $favIcon['path'] ?? null;
                        $settings->save();
                    }
                    
                    //Social Media links save
                    if(isset($request->socialmedia) && !empty($request->socialmedia)) {
                        foreach ($request->socialmedia as $key => $value) {
                            $this->masterSettingsRepository->create([
                                'title' => $key,
                                'url' => $value,
                                'type' => 'social_media'
                            ]);
                        }
                    }


                    return $this->objectCreated('Settings added successfully.', $settings);
                }

                return $this->validation('Failed to add settings details.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->back()->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    public function updateSettings(SettingsUpdateRequest $request){
        try {
            $siteDetails = $this->masterSettingsRepository->getDetailsByType('site_details')->first();
            if(!$siteDetails){
                return redirect()->back()->with('failed', 'Invalid request to update settings');
            }

            $response = DB::transaction(function () use ($request, $siteDetails) {

                $data = [
                    'title' => $request->name,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'copyrights' => $request->copy_rights,
                    'type' => 'site_details'
                ];

                if ($siteDetails->update($data)) {

                    if(isset($request->logo) && !empty($request->logo)){
                        $logo = $this->uploadWithName($request, 'logo', 'uploads/settings');
                        if(isset($logo['path']) && Storage::exists($logo['path'])){
                            Storage::delete($siteDetails->logo);
                        }
                        $siteDetails->logo = $logo['path'] ?? $siteDetails->logo;
                        $siteDetails->save();
                    }

                    if(isset($request->fav_icon) && !empty($request->fav_icon)){
                        $favIcon = $this->uploadWithName($request, 'fav_icon', 'uploads/settings');
                        if(isset($favIcon['path']) && Storage::exists($favIcon['path'])){
                            Storage::delete($siteDetails->fav_icon);
                        }
                        $siteDetails->fav_icon = $favIcon['path'] ?? $siteDetails->fav_icon;
                        $siteDetails->save();
                    }
                    
                    //Social Media links save
                    if(isset($request->socialmedia) && !empty($request->socialmedia)) {
                        $this->masterSettingsRepository->getDetailsByType('social_media')->delete();
                        foreach ($request->socialmedia as $key => $value) {
                            $this->masterSettingsRepository->create([
                                'title' => $key,
                                'url' => $value,
                                'type' => 'social_media'
                            ]);
                        }
                    }


                    return $this->success('Settings updated successfully.', $siteDetails);
                }

                return $this->validation('Failed to update settings details.');
            });

            $response = $response->getData();
            if($response->status){
                return redirect()->back()->with('success', $response->message);
            }

            return redirect()->back()->with('failed', $response->message);

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
