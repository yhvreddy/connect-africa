<?php

namespace App\Http\Controllers\Apis\v1;

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
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SiteSettingsResource;

class SettingsController extends Controller
{
    use HttpResponses, TruFlix;

    protected $masterSettingsRepository;

    public function __construct(
        MasterSettingsRepository $masterSettingsRepository
    ) {
        $this->masterSettingsRepository = $masterSettingsRepository;
    }

    /**
     * @OA\Get(
     *   tags={"Settings"},
     *   path="/api/v1/settings/site",
     *   summary="Get Settings Details",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *   )
     * )
     */
    public function settings(){
        $siteDetails = $this->masterSettingsRepository->getDetailsByType('site_details')->first();
        $siteDetails->socialMediaDetails = $this->masterSettingsRepository->getDetailsByType('social_media')
                                            ->select('id', 'title', 'url')->get()->toArray();
        return $this->success('Site Settings Data', new SiteSettingsResource($siteDetails));
    }

}
