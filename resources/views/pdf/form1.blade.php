<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind in One File</title>
    <!-- Include Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style lang="scss" scoped>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap');

        body {
            /* เรียกใช้งาน Font */
            font-family: 'Kanit', sans-serif;
        }

        .title {
            color: var(--text-color-text-grey-darkest, #1f1d1f);
            /* fields/title */
            font-family: Kanit;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: 24px;
            /* 150% */
            letter-spacing: 0.1px;
        }


        .PC_Body1-R {
            font-family: Kanit;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 160%;
        }

        .PC_Body1-SB {
            font-family: Kanit;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: 160%;
        }

        .PC_H2-SB {
            font-family: Kanit;
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: 140%;
        }

        .PC_H2-R {
            font-family: Kanit;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: 140%;
        }

        .PC_H3_R {
            font-family: Kanit;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 156%;
        }

        .PC_H1-SB {
            font-family: Kanit;
            font-size: 32px;
            font-style: normal;
            font-weight: 600;
            line-height: 38px;
            /* 118.75% */
        }

        .PC_H2-SB {
            font-size: 24px;
            font-weight: 600;
        }

        .PC_Body1-SB {
            font-size: 16px;
            font-weight: 600;
        }

        .PC_Body2-R {
            font-family: Kanit;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 160%;
        }

        .PC_H3-SB {
            font-family: Kanit;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: 156%;
        }

        p,
        span,
        div {
            break-inside: auto;
        }
    </style>

</head>

<html>

<body>
    <div class="w-full flex flex-col items-start gap-[26px]" ref="pdfRef">
        <div class="w-full flex flex-col items-start gap-[20px]">
            <div class="w-full flex flex-col items-start gap-[6px]">
                <div class="PC_H2-SB text-[#1F1D1F]">รายงานการปฏิบัติการฉุกเฉิน</div>
            </div>
            <div class="w-full flex items-center gap-[8px]">
                <div class="w-full flex items-center justify-between gap-[10px] flex-wrap sm:flex-nowrap">
                    <div class="flex items-center gap-[20px] flex-wrap sm:flex-nowrap">
                        <div class="flex flex-col">
                            <div class="PC_Body2-R text-[#7C7379]">ครั้งที่</div>
                            <div class="PC_Body1-R text-[#1F1D1F]">{{ $information['count'] ?? 0 }}</div>
                        </div>
                        <div class="h-[47px] border-r-[1px] border-[#DFDCDD]"></div>
                        <div class="flex flex-col">
                            <div class="PC_Body2-R text-[#7C7379]">วันที่ประเมิน</div>
                            <div class="PC_Body1-R text-[#1F1D1F]">
                                {{ $information['created_at'] ?? "-" }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full border border-[#DFDCDD] border-dashed"></div>

            <!-- -- select team-- -->
            <div>
                <div class="PC_Body2-R text-[#7C7379]">ทีมปฏิบัติการฉุกเฉิน</div>
                <div class="PC_Body1-R text-[#1F1D1F]">{{ $information['team_name'] ?? "-" }}</div>
            </div>
        </div>
        <!-- info -->
        <div class="w-full flex flex-col items-start gap-[20px]">
            <div class="w-full p-[10px] bg-[#FBEFF8]">
                <span class="PC_Body1-SB text-[#1F1D1F]"> 1. ข้อมูลพื้นฐาน </span>
            </div>
            <div class="w-full flex max-[500px]:flex-col items-start max-[500px]:items-center gap-[20px]">
                <div class="min-w-[100px] min-h-[100px] w-[100px] h-[100px] border-[1px] border-solid border-[#F2E7F9]">
                    <img src="{{ $information['population_image'] ?? 'https://placehold.co/100x100/png' }}">
                </div>
                <div class="w-full flex flex-col items-start gap-[24px]">
                    <div class="w-full flex flex-col items-start gap-[2px]" style="page-break-inside: avoid;">
                        <div class="PC_Body2-R text-[#7C7379]">ชื่อผู้ประสบภัย</div>
                        <div class="PC_Body1-R text-[#1F1D1F]">
                            {{ $information['name_of_assessment'] ?? "-" }}
                        </div>
                    </div>

                    <div class="w-full flex flex-col items-start gap-[2px]" style="page-break-inside: avoid;">
                        <div class="PC_Body2-R text-[#7C7379]">วันเกิด</div>
                        <div class="PC_Body1-R text-[#1F1D1F]">
                           {{ $information['birth_date'] ?? "-"}} ( {{$information['age'] ?? "-" }} ปี)
                        </div>
                    </div>

                    <div class="w-full flex flex-col items-start gap-[2px]" style="page-break-inside: avoid;">
                        <div class="PC_Body2-R text-[#7C7379]">เพศ</div>
                        <div class="PC_Body1-R text-[#1F1D1F]">
                            {{ $information['gender'] ?? "-" }}
                        </div>
                    </div>

                    <div class="w-full flex flex-col items-start gap-[2px]" style="page-break-inside: avoid;">
                        <div class="PC_Body2-R text-[#7C7379]">ที่อยู่ตามภูมิลำเนา</div>
                        <div class="PC_Body1-R text-[#1F1D1F]">
                            {{ $information['address'] ?? "-" }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- info -->

        <!-- 2. การเจ็บป่วย -->
         @if(count($data) > 0)
        <div class="w-full flex flex-col items-start gap-[20px]">
            <div class="w-full p-[10px] bg-[#FBEFF8]">
                <label class="PC_Body1-SB text-[#1F1D1F]"> 2. Health events</label>
            </div>
            @foreach($data as $item)
            <div class="page-keep h-[30px] w-full flex items-center gap-[10px]" style="page-break-inside: avoid;">
                <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                <span class="PC_Body1-SB text-[#1F1D1F] block h-[30px]">
                    {{$item['topic']}}
                </span>
            </div>
            @foreach($item['data'] as $data)
            <div class="flex flex-col items-start gap-[20px]" style="page-break-inside: avoid;">
                <div class="page-keep flex items-center">
                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                    {{$data}}
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
        @endif

        <div class="mb-[26px] w-full border border-[#DFDCDD] border-dashed" style="page-break-inside: avoid;">
        </div>

        <div class="page-keep w-full flex items-center gap-[10px]" style="page-break-inside: avoid;">
            <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
            <span class="PC_Body1-SB text-[#1F1D1F]">ผู้ลงข้อมูล</span>
        </div>

        <div class="flex flex-col items-start gap-[20px]" style="page-break-inside: avoid;">
            {{ $information['created_name'] ?? "-" }}
        </div>
    </div>
</body>

</html>
