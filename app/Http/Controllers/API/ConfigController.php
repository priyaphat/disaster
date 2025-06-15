<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Config;
use Illuminate\Http\Request;
use Spatie\FlareClient\Api;

class ConfigController extends Controller
{

    /**
     * Config | ดึง config ตัวแปร / ปุ้ย
     */
    public function index(Request $request, $id = null)
    {
        $user = $request->user;
        $population = $request->population;
        $data = Config::select('*');
        if ($id) $data = $data->where('id', $id);
        if ($request->type) $data = $data->whrer('type', $request->type);
        if ($request->status) $data = $data->where('status', $request->status);
        if (!is_null($user)) $data = $data->where('description->user', $user);
        if (!is_null($population)) $data = $data->where('description->population', $population);

        $data = $data->get();
        $config = [];
        foreach ($data as $item) {
            if ($item->type == 2) {
                $name = [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            } else {
                $config[] = $item;
            }
        }
        if (isset($name)) $res['name'] = $name;
        $res['config'] = $config;
        if ($id) return ApiResponse::successResponse(__('success.data'), $config);
        return ApiResponse::successResponse(__('success.data'), $res);
    }

    /**
     * Config | สร้าง config ตัวแปร / ปุ้ย
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:configs,name',
            'type' => 'nullable|integer|in:1,2',
            'description' => 'nullable|array',
            'status' => 'nullable|integer|in:0,1',
        ]);
        $data = Config::create($data);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Config | อัพเดต config ตัวแปร / ปุ้ย
     */
    public function update(Request $request, Config $config)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255|unique:configs,name,' . $config->id,
            'type' => 'nullable|integer|in:1,2',
            'description' => 'nullable|array',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $config->update($request->all());
        return ApiResponse::successResponse(__('success.config.update'), $config);
    }

    /**
     * Config | reset config ครับ จะรี แบบไหน เปิด หรือ ปิด 
     */
    public function reset()
    {
        Config::where('type', 1)->update([
            'description->population' => 0,
            'description->user' => 0,
        ]);
        return ApiResponse::successResponse(__('success.config.reset'));
    }

    /**
     * Config | ลบ config
     */
    public function destroy(Config $config)
    {
        $config->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
