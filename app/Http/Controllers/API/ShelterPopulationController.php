<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Population\ExitAllPopulationRequest;
use App\Http\Requests\Population\TransferPopulationRequest;
use App\Http\Requests\ShelterPosition\AssignShelterPositionRequest;
use App\Http\Requests\ShelterStaff\CreateOneStaffRequest;
use App\Http\Requests\ShelterStaff\CreateShelterStaffRequest;
use App\Http\Requests\ShelterStaff\ExitAllShelterStaffRequest;
use App\Http\Requests\ShelterStaff\TransferShelterStaffRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LinePostUtility;
use App\Models\Population;
use App\Models\ShelterPopulation;
use App\Models\ShelterStaff;
use App\Services\ShelterPopulationService;
use App\Services\ShelterService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ShelterPopulationController extends Controller
{
    use AuthorizesRequests;
    protected $shelterService, $shelterPopService;

    public function __construct(ShelterService $shelterService, ShelterPopulationService $shelterPopService)
    {
        $this->shelterService = $shelterService;
        $this->shelterPopService = $shelterPopService;
    }

    /**
     * Transfer population | ย้ายศูนย์พักพิงประชากรรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function transfer(TransferPopulationRequest $request, Population $population)
    {
        $this->authorize('update', $population);

        $oldShelter = $population->shelter_id;
        $newShelter = $request->shelter_id;

        if ($newShelter == $oldShelter) {
            return ApiResponse::successResponse(__('success.updated'));
        }

        $data = $this->shelterPopService->update($population, $request->all());
        return ApiResponse::successResponse(__('success.population.tranfered'), $data);
    }

    /**
     * Exit population | นำประชากรออกจากศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('population', description: 'ส่ง id ของ populations', type: 'string', example: '25')]
    public function exit(Population $population)
    {
        $this->authorize('update', $population);

        $data = $this->shelterPopService->delete($population);
        return ApiResponse::successResponse(__('success.population.leaved'), $data);
    }

    /**
     * Exit all population | นำประชากรออกจากศูนย์พักพิงทั้งหมด
     */
    public function exitAll(ExitAllPopulationRequest $request)
    {
        $this->authorize('updateAll');

        $this->shelterPopService->deleteByShelter($request->all());
        return ApiResponse::successResponse(__('success.population.leaved'));
    }
}
