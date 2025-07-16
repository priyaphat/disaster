<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Config;
use Carbon\Carbon;
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
        if ($request->type) {
            $data = $data->where('type', $request->type);
        } else {
            $data = $data->whereIn('type', [1, 2]);
        }
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
        $data['status'] = $data['status'] ?? 0;
        $data = Config::create($data);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Config | อัพเดต config ตัวแปร / ปุ้ย
     */
    public function update(Request $request, Config $config)
    {
        $data = $request->validate([
            // 'name' => 'nullable|string|max:255|unique:configs,name,' . $config->id,
            // 'type' => 'nullable|integer|in:1,2',
            // 'description' => 'nullable|array',
            // 'status' => 'nullable|integer|in:0,1',
            'population' => 'nullable|integer|in:0,1',
            'user' => 'nullable|integer|in:0,1',
        ]);

        // $config->update($request->all());

        $description = $config->description ?? [];
        if (isset($request->population)) $description['population'] = $request->population;
        if (isset($request->user)) $description['user'] = $request->user;
        $config->description = $description;
        $config->save();

        if (in_array(0, [$request->user, $request->population], true)) {
            return ApiResponse::successResponse(__('success.config.update_off'), $config);
        }
        return ApiResponse::successResponse(__('success.config.update_on'), $config);
    }

    /**
     * Config | reset config ครับ จะรี แบบไหน เปิด หรือ ปิด 
     */
    public function reset()
    {
        // Config::where('type', 1)->update([
        //     'description->population' => 0,
        //     'description->user' => 0,
        // ]);
        Config::where('type', 1)->chunkById(100, function ($configs) {
            foreach ($configs as $config) {
                $description = $config->description;
                if (isset($description['population'])) $description['population'] = 0;
                if (isset($description['user'])) $description['user'] = 0;
                $config->description = $description;
                $config->save();
            }
        });
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

    public function weather()
    {
        //tc อุณหภูมิ,rh ความชื้น,cond สถานะ ,rain ฝน% ,ws10m ลม m/s
        $config = Config::select('description')->where('type', Config::weather)->first();
        $data = $config ? $config->description : null;
        if ($data) {
            $now = Carbon::now('Asia/Bangkok')->hour;
            // $today = collect($data['hour'])->first(function ($item) use ($now) {
            //     $itemHour = Carbon::parse($item['time'])->hour;
            //     return $itemHour >= $now;
            // });
            // $data['today'] = $today['data'];

            $data['hour'] = collect($data['hour'])->filter(function ($item) use ($now) {
                $itemHour = Carbon::parse($item['time'])->hour;
                return $itemHour >= $now;
            })->values();

            $data['today'] = $data['hour']->first();
        }
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
