<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'ต้องยอมรับ :attribute',
    'active_url' => ':attribute ไม่ใช่ URL ที่ถูกต้อง',
    'after' => ':attribute ต้องเป็นวันที่หลังจาก :date',
    'after_or_equal' => ':attribute ต้องเป็นวันที่หลังหรือเท่ากับ :date',
    'alpha' => ':attribute สามารถประกอบด้วยอักษรเท่านั้น',
    'alpha_dash' => ':attribute สามารถประกอบด้วยอักษร, ตัวเลข, ขีดกลาง และขีดล่างเท่านั้น',
    'alpha_num' => ':attribute สามารถประกอบด้วยอักษรและตัวเลขเท่านั้น',
    'array' => ':attribute ต้องเป็นอาร์เรย์',
    'before' => ':attribute ต้องเป็นวันที่ก่อน :date',
    'before_or_equal' => ':attribute ต้องเป็นวันที่ก่อนหรือเท่ากับ :date',
    'between' => [
        'numeric' => ':attribute ต้องอยู่ระหว่าง :min และ :max',
        'file' => ':attribute ต้องมีขนาดระหว่าง :min และ :max กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวระหว่าง :min และ :max ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการระหว่าง :min และ :max',
    ],
    'boolean' => ':attribute ต้องเป็นจริงหรือเท็จ',
    'confirmed' => ':attribute การยืนยันไม่ตรงกัน',
    'date' => ':attribute ไม่ถูกต้อง',
    'date_equals' => ':attribute ต้องเป็นวันที่เท่ากับ :date',
    'date_format' => ':attribute ต้องตรงกับรูปแบบ :format',
    'different' => ':attribute และ :other ต้องแตกต่างกัน',
    'decimal' => ':attribute ต้องมี ทศนิยม :decimal ตำแหน่ง',
    'digits' => ':attribute ต้องมี :digits หลัก',
    'digits_between' => ':attribute ต้องมีจำนวนหลักระหว่าง :min และ :max',
    'dimensions' => ':attribute มีขนาดรูปภาพที่ไม่ถูกต้อง',
    'distinct' => ':attribute มีค่าซ้ำ',
    'email' => 'กรุณาระบุ :attribute ให้ถูกต้อง',
    'ends_with' => ':attribute ต้องลงท้ายด้วยหนึ่งในค่าต่อไปนี้: :values',
    'exists' => ':attribute ที่เลือกไม่ถูกต้อง',
    'file' => ':attribute ต้องเป็นไฟล์',
    'filled' => ':attribute ต้องมีค่า',
    'gt' => [
        'numeric' => ':attribute ต้องมากกว่าค่า :value',
        'file' => ':attribute ต้องมีขนาดมากกว่าค่า :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวมากกว่าค่า :value ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการมากกว่าค่า :value',
    ],
    'gte' => [
        'numeric' => ':attribute ต้องมากกว่าหรือเท่ากับค่า :value',
        'file' => ':attribute ต้องมีขนาดมากกว่าหรือเท่ากับค่า :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวมากกว่าหรือเท่ากับค่า :value ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการ :value ขึ้นไป',
    ],
    'image' => ':attribute ต้องเป็นรูปภาพ',
    'in' => ':attribute ไม่ถูกต้อง',
    'in_array' => ':attribute ไม่พบใน :other',
    'integer' => ':attribute ต้องเป็นจำนวนเต็ม',
    'ip' => ':attribute ต้องเป็นที่อยู่ IP ที่ถูกต้อง',
    'ipv4' => ':attribute ต้องเป็นที่อยู่ IPv4 ที่ถูกต้อง',
    'ipv6' => ':attribute ต้องเป็นที่อยู่ IPv6 ที่ถูกต้อง',
    'json' => ':attribute ต้องเป็นสตริง JSON ที่ถูกต้อง',
    'lt' => [
        'numeric' => ':attribute ต้องน้อยกว่าค่า :value',
        'file' => ':attribute ต้องมีขนาดน้อยกว่าค่า :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวน้อยกว่าค่า :value ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการน้อยกว่าค่า :value',
    ],
    'lte' => [
        'numeric' => ':attribute ต้องน้อยกว่าหรือเท่ากับค่า :value',
        'file' => ':attribute ต้องมีขนาดน้อยกว่าหรือเท่ากับค่า :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวน้อยกว่าหรือเท่ากับค่า :value ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการไม่เกินค่า :value',
    ],
    'max' => [
        'numeric' => 'ขออภัย โปรดกรอกข้อความไม่เกิน :max ตัวอักษร',
        'file' => ':attribute มีขนาดเกิน :max กิโลไบต์',
        'string' => 'ขออภัย โปรดกรอกข้อความไม่เกิน :max ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการไม่เกิน :max',
    ],
    'mimes' => ':attribute ต้องเป็นไฟล์ประเภท: :values',
    'mimetypes' => ':attribute ต้องเป็นไฟล์ประเภท: :values',
    'min' => [
        'numeric' => ':attribute ต้องไม่น้อยกว่า :min',
        'file' => ':attribute ต้องมีขนาดอย่างน้อย :min กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนรายการอย่างน้อย :min',
    ],
    'not_in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'not_regex' => ':attribute รูปแบบไม่ถูกต้อง',
    'numeric' => ':attribute ต้องเป็นตัวเลขเท่านั้น',
    'password' => [
        'mixed' => ':attribute ต้องมีตัวอักษรพิมพ์เล็กและพิมพ์ใหญ่',
        'letters' => ':attribute ต้องมีตัวอักษรอย่างน้อย 1 ตัว',
        'symbols' => ':attribute ต้องมีสัญลักษณ์อย่างน้อย 1 ตัว',
        'numbers' => ':attribute ต้องมีตัวเลขอย่างน้อย 1 ตัว',
        'uncompromised' => ':attribute ปรากฏในข้อมูลที่รั่วไหล โปรดใช้รหัสผ่านอื่น',
        'thai' => ':attribute ไม่สามารถตั้งรหัสผ่านเป็นภาษาไทยได้',
        'min_length' => ':attribute ต้องตั้งรหัสผ่านความยาวอย่างน้อย 8 ตัวอักษรขึ้นไป',
    ],
    'present' => ':attribute ต้องมีอยู่',
    'regex' => ':attribute รูปแบบไม่ถูกต้อง',
    'required' => 'กรุณาระบุ :attribute',
    'size' => [
        'numeric' => ':attribute ต้องเป็น :size',
        'file' => ':attribute ต้องมีขนาด :size กิโลไบต์',
        'string' => ':attribute ต้องมีความยาว :size ตัวอักษร',
        'array' => ':attribute ต้องมี :size รายการ',
    ],
    'starts_with' => ':attribute ต้องเริ่มต้นด้วยหนึ่งในค่าต่อไปนี้: :values',
    'string' => ':attribute ต้องเป็นสตริง',
    'timezone' => ':attribute ต้องเป็นเขตเวลา (zone) ที่ถูกต้อง',
    'unique' => 'ขออภัย :attributeนี้ ถูกใช้งานแล้ว',
    'uploaded' => ':attribute เกิดข้อผิดพลาดในการอัปโหลด',
    'url' => ':attribute รูปแบบไม่ถูกต้อง',
    'uuid' => ':attribute ต้องเป็น UUID ที่ถูกต้อง',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'fullname' => [
            'exists' => 'ขออภัย ชื่อ-นามสกุลนี้ ถูกใช้งานแล้ว'
        ],
        'phone' => [
            'regex' => 'โปรดกรอกเบอร์โทรศัพท์ที่ติดต่อ ให้ถูกต้อง'
        ],
        'email' => [
            // 'regex' => 'อีเมลต้องประกอบด้วยตัวอักษรหรือตัวเลข และห้ามลงท้ายด้วยจุด (.), ขีดกลาง (-) หรือขีดล่าง (_)',
            'regex' => 'ขออภัย โปรดกรอกข้อมูลให้ถูกต้อง และกรอกอักขระพิเศษได้เฉพาะ .-_'
        ],
        'username' => [
            'regex' => 'ขออภัย ชื่อผู้ใช้งานต้องขึ้นต้นด้วยตัวอักษรภาษาอังกฤษ และต้องมี 5 - 30 ตัวอักษร รวมตัวเลข และ อักขระพิเศษ .-_',
        ],
        'endtime' => [
            'after' => 'ขออภัย เวลาเริ่มต้น จะต้องเป็นเวลาก่อนเวลาสิ้นสุด โปรดตรวจสอบความถูกต้อง',
        ],
        'categories' => [
            'name' => 'ขออภัยไม่สามารถใช้ชื่อประเภทซ้ำได้',
        ],
        'unit' => [
            'name' => 'ขออภัยไม่สามารถใช้ชื่อลักษณนามซ้ำได้',
        ],
        'product' => [
            'name' => 'ขออภัย ชื่อทรัพยากร และลักษณนามที่เลือกซ้ำ',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'ชื่อ',
        'username' => 'ชื่อผู้ใช้งาน',
        'email' => 'อีเมล',
        'password' => 'รหัสผ่าน',
        'password_confirmation' => 'ยืนยันรหัสผ่าน',
        'prefix_id' => 'คำนำหน้าชื่อ',
        'firstname' => 'ชื่อ',
        'lastname' => 'นามสกุล',
        'phone' => 'เบอร์โทรศัพท์',
        'type' => 'ประเภทเจ้าหน้าที่',
        'department_id' => 'สังกัดภาคส่วน',
        'district_id' => 'พื้นที่ ที่สังกัด',
        'province_id' => 'จังหวัด',
        'subdistrict_id' => 'ตำบล ที่สังกัด',
        'position_id' => 'ตำแหน่งงาน',
        'user_id' => 'ผู้ใช้งาน',
        'team_id' => 'ทีมที่สังกัด',
        'created_date' => 'วันที่สร้าง',
        'leader_id' => 'หัวหน้า',
        'geography_id' => 'ID ของที่อยู่',
        'province_code' => 'จังหวัด',
        'district_code' => 'อำเภอ',
        'subdistrict_code' => 'ตำบล',
        'address' => 'ที่อยู่',
        'utilities' => 'สาธารณูปโภคประจำศูนย์',
        'max_capacity' => 'ระบุความจุ',

        //disaster
        'latitude' => 'ละติจูด',
        'longitude' => 'ลองติจูด',
        'depth' => 'ความลึก',
        'magnitude' => 'แรงสั่น',
        'occurrence_time' => 'เวลา เหตุการณ์',
        'line_oa' => 'ไลน์ OA',

        'water_source' => 'แหล่งน้ำ',
        'water_source_type' => 'ประเภทแหล่งน้ำ',
        'status' => 'สถานะ',
        'water_value' => 'ระดับน้ำ',

        "source" => 'แหล่งที่มา',
        "smog_value" => 'ค่าหมอก',

        'victim_name.*.prefix_id' => 'คำนำหน้า',
        'victim_name.*.first_name' => 'ชื่อ',
        'victim_name.*.last_name' => 'นามสกุล',
        'victim_name.*.birth_date' => 'วันเกิด',
        'victim_name.*.gender' => 'เพศ',
        'victim_name.*.phone' => 'เบอร์โทรศัพท์',
        'victim_name.*.p_address' => 'ที่อยู่ตามทะเบียนบ้าน',
        'victim_name.*.p_geography_id' => 'จังหวัดตามทะเบียนบ้าน',
        'victim_name.*.c_address' => 'ที่อยู่ปัจจุบัน',
        'victim_name.*.c_geography_id' => 'จังหวัดที่อยู่ปัจจุบัน',
        'victim_name.*.same_address' => 'ใช้ที่อยู่เดียวกัน',
        'victim_name.*.status' => 'สถานะบุคคล',
        'victim_name.*.created_date' => 'วันที่บันทึก',

        'shelter_type_id' => 'ประเภทของศูนย์พักพิง',
    ],

];
