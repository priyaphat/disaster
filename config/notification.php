<?php

return [
    'default' => [
        'title' => 'ทดสอบแจ้งเตือน',
        'message' => [
            ['type' => 'text', 'content' => 'ทดสอบแจ้งเตือน'],
        ]
    ],
    'post' => [
        'created' => [
            'title' => 'ประกาศภัยพิบัติ',
            'message' => [
                ['type' => 'text', 'content' => '#{disaster}'],
                ['type' => 'text', 'content' => 'ระดับ #{level}'],
                ['type' => 'text', 'content' => 'พื้นที่ #{area}']
            ]
        ],
        'updated' => [
            'title' => 'อัปเดตภัยพิบัติ',
            'message' => [
                ['type' => 'text', 'content' => '#{disaster}'],
                ['type' => 'text', 'content' => 'ระดับ #{level}'],
                ['type' => 'text', 'content' => 'พื้นที่ #{area}']
            ]
        ],
        'deleted' => [
            'title' => 'ยุติภัยพิบัติ',
            'message' => [
                ['type' => 'text', 'content' => '#{disaster}'],
                ['type' => 'text', 'content' => 'ระดับ #{level}'],
                ['type' => 'text', 'content' => 'พื้นที่ #{area}']
            ]
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
    'product' => [
        'received' => [
            'title' => 'ได้รับทรัพยากรระหว่างศูนย์',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้'],
                ['type' => 'text', 'content' => 'ศูนย์กลาง'],
                ['type' => 'text', 'content' => 'ได้รับ'],
                ['type' => 'bold', 'content' => '#{product} (#{unit})'],
                ['type' => 'text', 'content' => 'จำนวน'],
                ['type' => 'bold', 'content' => '#{amount}'],
                ['type' => 'text', 'content' => 'จาก'],
                ['type' => 'bold', 'content' => 'ศูนย์พักพิง#{shelter}'],

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
        ],
        'received' => [
            'title' => 'ได้รับทรัพยากรระหว่างศูนย์',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้'],
                ['type' => 'text', 'content' => 'ศูนย์พักพิง#{shelter}'],
                ['type' => 'text', 'content' => 'ได้รับ'],
                ['type' => 'bold', 'content' => '#{product} (#{unit})'],
                ['type' => 'text', 'content' => 'จำนวน'],
                ['type' => 'bold', 'content' => '#{amount}'],
                ['type' => 'text', 'content' => 'จาก'],
                ['type' => 'bold', 'content' => 'ศูนย์กลาง'],
            ]
        ]
    ],
    'shelter_staff' => [
        'transfered' => [
            'title' => 'ถูกย้ายเข้าสู่ศูนย์พักพิง',
            'message' => [
                ['type' => 'text', 'content' => 'คุณถูกย้ายเข้าสู่ศูนย์พักพิง'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'leaved' => [
            'title' => 'ถูกนำออกจากศูนย์พักพิง',
            'message' => [
                ['type' => 'text', 'content' => 'คุณถูกนำออกจากศูนย์พักพิง'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ]
    ],
    'position' => [
        'updated' => [
            'title' => 'อัปเดต ระบบบัญชาการ',
            'message' => [
                ['type' => 'text', 'content' => 'ขณะนี้ มีการปรับเปลี่ยนตำแหน่งงานในระบบบัญชาการ'],
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
        'shelter' => [
            'created' => [
                'title' => 'นัดหมายตรวจเยี่ยม ใหม่',
                'message' => [
                    ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{date} เวลา #{time} กับ ทีมเจ้าหน้าที่'],
                    ['type' => 'bold', 'content' => '#{team}'],
                ]
            ],
            'updated' => [
                'title' => 'นัดหมายตรวจเยี่ยม ถูกอัปเดต',
                'message' => [
                    ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{old_date} เวลา #{old_time} กับ ทีมเจ้าหน้าที่'],
                    ['type' => 'bold', 'content' => '#{old_team}'],
                    ['type' => 'text', 'content' => 'ถูกแก้ไขเป็น #{new_date} เวลา #{new_time} กับ ทีมเจ้าหน้าที่'],
                    ['type' => 'bold', 'content' => '#{new_team}'],
                ]
            ],
            'deleted' => [
                'title' => 'นัดหมายตรวจเยี่ยม ถูกยกเลิก',
                'message' => [
                    ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{date} เวลา #{time} กับ ทีมเจ้าหน้าที่'],
                    ['type' => 'bold', 'content' => '#{team}'],
                    ['type' => 'text', 'content' => 'ถูกยกเลิก'],
                ]
            ],
        ],
        'team' => [
            'created' => [
                'title' => 'นัดหมายตรวจเยี่ยม',
                'message' => [
                    ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{date} เวลา #{time} ที่ ศูนย์พักพิง'],
                    ['type' => 'bold', 'content' => '#{shelter}'],
                ]
            ],
            'updated' => [
                'title' => 'นัดหมายตรวจเยี่ยม ถูกอัปเดต',
                'message' => [
                    ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{old_date} เวลา #{old_time} ที่ ศูนย์พักพิง'],
                    ['type' => 'bold', 'content' => '#{old_shelter}'],
                    ['type' => 'text', 'content' => 'ถูกแก้ไขเป็น #{new_date} เวลา #{new_time} ที่ ศูนย์พักพิง'],
                    ['type' => 'bold', 'content' => '#{new_shelter}'],
                ]
            ],
            'deleted' => [
                'title' => 'นัดหมายตรวจเยี่ยม ถูกยกเลิก',
                'message' => [
                    ['type' => 'text', 'content' => 'มีนัดหมาย ในวันที่ #{date} เวลา #{time} ที่ ศูนย์พักพิง'],
                    ['type' => 'bold', 'content' => '#{shelter}'],
                    ['type' => 'text', 'content' => 'ถูกยกเลิก'],
                ]
            ],
        ]
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
        'approved' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'ทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{team}'],
                ['type' => 'text', 'content' => 'รับคำร้องจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'rejected' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'ทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{team}'],
                ['type' => 'text', 'content' => 'ปฏิเสธคำร้องจาก'],
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
        'unsuccess' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'คำร้องจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'เข้าช่วยเหลือไม่สำเร็จ'],
            ]
        ],
        'assigned' => [
            'title' => 'เหตุฉุกเฉิน',
            'message' => [
                ['type' => 'text', 'content' => 'ได้รับมอบหมาย เข้าช่วยเหลือ คำขอจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'canceled' => [
            'title' => 'เหตุฉุกเฉิน ถูกยกเลิก',
            'message' => [
                ['type' => 'text', 'content' => 'คำขอจาก'],
                ['type' => 'bold', 'content' => '#{name}'],
                ['type' => 'text', 'content' => 'ถูกยกเลิก'],
            ]
        ],
    ],
    'user' => [
        'registered' => [
            'title' => 'รายการรอลงทะเบียนเจ้าหน้าที่',
            'message' => [
                ['type' => 'text', 'content' => 'มีรายการลงทะเบียนเจ้าหน้าที่จาก'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
    ],
    'team' => [
        'joined' => [
            'title' => 'ถูกย้ายเข้าทีมเจ้าหน้าที่',
            'message' => [
                ['type' => 'text', 'content' => 'คุณถูกย้ายเข้าทีมเจ้าหน้าที่'],
                ['type' => 'bold', 'content' => '#{name}'],
            ]
        ],
        'leaved' => [
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
    ],
];
