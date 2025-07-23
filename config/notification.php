<?php

return [
    'disaster' => [
        'created' => [
            'title' => 'ประกาศภัยพิบัติ',
            'message' => [
                ['type' => 'text', 'content' => '#{disaster}'],
                ['type' => 'text', 'content' => '#{level}'],
                ['type' => 'text', 'content' => '#{area}']
            ]
        ],
        'updated' => [
            'title' => 'อัปเดตภัยพิบัติ',
            'message' => [
                ['type' => 'text', 'content' => '#{disaster}'],
                ['type' => 'text', 'content' => '#{level}'],
                ['type' => 'text', 'content' => '#{area}']
            ]
        ],
        'deleted' => [
            'title' => 'ยุติภัยพิบัติ',
            'message' => ['type' => 'text', 'content' => '#{disaster}'],
        ]
    ],

    'shelter' => [
        'opened' => [
            'title' => 'เปิดใช้งานศูนย์พักพิง',
            // 'message' => 'ศูนย์พักพิง กลางเวียงเชียงรายแห่งที่ 1 เปิดใช้งานแล้ว',
            'message' => [
                ['type' => 'text', 'content' => 'ศูนย์พักพิง'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'เปิดใช้งานแล้ว'],
            ]
        ],
        'closed' => [
            'title' => 'ปิดใช้งานศูนย์พักพิง',
            'message' => [
                ['type' => 'text', 'content' => 'ศูนย์พักพิง'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'ปิดใช้งานแล้ว'],
            ]
        ]
    ],
    'damaged' => [
        'updated' => [
            'title' => 'อัปเดต สาธารณประโยชน์ที่ได้รับความเสียหาย',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'ปิดทำการ #{total} แห่ง'],
            ]
        ]
    ],
    'shelter_product' => [
        'updated' => [
            'title' => 'อัปเดต ทรัพยากรภายในศูนย์',
            'message' => [
                ['type' => 'text', 'content' => '#{product} (#{unit})'],
                ['type' => 'text', 'content' => 'คงเหลือ'],
                ['type' => 'text', 'content' => '#{total}'],
            ]
        ]
    ],
    'shelter_position' => [
        'updated' => [
            'title' => 'อัปเดต แผนผังการทำงาน',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้ มีการปรับเปลี่ยนตำแหน่งงานภายในศูนย์ใหม่'],
            ]
        ],
        'updated_leader' => [
            'title' => 'อัปเดต ระบบบัญชาการ',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้ มีการปรับเปลี่ยนตำแหน่งงานในระบบบัญชาการ'],
            ]
        ]
    ],
    'shelter_news' => [
        'created' => [
            'title' => 'ประกาศ ข่าวประชาสัมพันธ์ ประจำศูนย์พักพิง',
            'message' => [
                ['type' => 'text', 'content' => '#{title}'],
            ]
        ],
        'updated' => [
            'title' => 'อัปเดต ข่าวประชาสัมพันธ์ ประจำศูนย์พักพิง',
            'message' => [
                ['type' => 'text', 'content' => '#{title}'],
            ]
        ]
    ],
    'appointment' => [
        'created' => [
            'title' => 'นัดหมายตรวจเยี่ยม ใหม่',
            'message' => [
                ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{appointment_date} #{shift} กับ ทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{team}'],
            ]
        ],
        'updated' => [
            'title' => 'นัดหมายตรวจเยี่ยม ถูกอัปเดต',
            'message' => [
                ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{old_date} #{old_shift} กับ ทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{old_team}'],
                ['type' => 'text', 'content' => ' ถูกแก้ไขเป็น #{new_date} #{new_shift} กับ ทีมเจ้าหน้าที่ '],
                ['type' => 'bold', 'content' => '#{new_team}'],
            ]
        ],
        'deleted' => [
            'title' => 'นัดหมายตรวจเยี่ยม ใหม่',
            'message' => [
                ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{appointment_date} #{shift} กับ ทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{team}'],
                ['type' => 'text', 'content' => 'ถูกยกเลิก'],
            ]
        ],
    ],
    'product_request' => [
        'created' => [
            'title' => 'คำร้องขอทรัพยากร',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้'],
                ['type' => 'text', 'content' => 'มีคำร้องขอ'],
                ['type' => 'text', 'content' => '#{product} #{unit}'],
            ]
        ]
    ],
    'news' => [
        'created' => [
            'title' => 'ประกาศ ข่าวประชาสัมพันธ์',
            'message' => [
                ['type' => 'text', 'content' => '#{title}'],
            ]
        ],
        'updated' => [
            'title' => 'อัปเดต ข่าวประชาสัมพันธ์',
            'message' => [
                ['type' => 'text', 'content' => '#{title}'],
            ]
        ]
    ],
    'emergency_request' => [
        'created' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้ มีคำร้องขอใหม่จาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'accepted' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'ทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{team}'],
                ['type' => 'text', 'content' => 'รับคำร้องจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'success' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'คำร้องจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'ได้รับการช่วยเหลือสำเร็จแล้ว'],
            ]
        ],
        'failed' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'คำร้องจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'เข้าช่วยเหลือไม่สำเร็จ'],
            ]
        ],
        'assign' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'ได้รับมอบหมาย เข้าช่วยเหลือ คำขอจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'cancel' => [
            'title' => 'เหตุฉุกเฉิน ถูกยกเลิก',
            'message' => [
                ['type' => 'text', 'content' => 'คำขอจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'ถูกยกเลิก'],
            ]
        ],
    ],
    'user' => [
        'register' => [
            'title' => 'รายการรอลงทะเบียนเจ้าหน้าที่',
            'message' => [
                ['type' => 'text', 'content' => 'มีรายการลงทะเบียนเจ้าหน้าที่จาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
    ],
    'team' => [
        'join' => [
            'title' => 'ถูกย้ายเข้าทีมเจ้าหน้าที่',
            'message' => [
                ['type' => 'text', 'content' => 'คุณถูกย้ายเข้าทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'leave' => [
            'title' => 'ถูกนำออกจากทีมเจ้าหน้าที่',
            'message' => [
                ['type' => 'text', 'content' => 'คุณถูกนำออกจากทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'updated_leader' => [
            'title' => 'อัปเดตหัวหน้าทีมใหม่',
            'message' => [
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'ถูกแต่งตั้งเป็นหัวหน้าทีมใหม่'],
            ]
        ],
    ]
];
