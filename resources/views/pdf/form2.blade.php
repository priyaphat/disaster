<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบบันทึกเวชระเบียนของทีมปฏิบัติการฉุกเฉิน</title>
    <!-- Include Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <style lang="scss" scoped>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap');

        body {
            font-family: 'Kanit', sans-serif;
        }

        * {
            box-sizing: border-box !important;
        }

        .flex {
            display: flex !important;
        }

        .w-full {
            width: 100% !important;
        }

        .sm\:flex-row {
            flex-direction: row !important;
        }

        .title {
            color: #1f1d1f;
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

        .keep-together {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* คลาสหลัก */
        .btn_shadow {
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            border-radius: 6px;
            border: 1px solid;
        }

        /* คลาสย่อยสำหรับ Cancel และ OnlyCancel */
        .btn_shadow--cancel {
            width: 100%;
            max-width: 150px;
            border-color: #b7b2b5;
            background: #fff;
            color: #1f1d1f;
        }
    </style>
</head>

<html>

@php
    $formKeys = ['illness' => 'การเจ็บป้วย', 'care' => 'หัตถการและผลการรักษา', 'context' => 'บริบท'];
@endphp


<body>
    <div class="w-full flex flex-col items-start gap-[26px]">
        <div class="w-full flex flex-col items-start gap-[20px]">
            <div class="w-full flex flex-col items-start gap-[6px]">
                <div class="PC_H2-SB text-[#1F1D1F]">
                    แบบบันทึกเวชระเบียนของทีมปฏิบัติการฉุกเฉิน
                </div>
                <div class="PC_H3-R text-[#1F1D1F]">(Medical Data Sheet+)</div>
            </div>
            <div class="w-full flex items-center gap-[8px]">
                <div class="w-full flex items-center justify-between gap-[12px] flex-wrap sm:flex-nowrap">
                    <div class="flex items-center gap-[20px] flex-wrap sm:flex-nowrap">
                        <div class="flex flex-col">
                            <div class="PC_Body2-R text-[#7C7379]">ครั้งที่</div>
                            <div class="PC_Body1-R text-[#1F1D1F]">{{ $information['count'] }}</div>
                        </div>
                        <div class="h-[47px] border-r-[1px] border-[#DFDCDD]"></div>
                        <div class="flex flex-col">
                            <div class="PC_Body2-R text-[#7C7379]">วันที่ประเมิน</div>
                            <div class="PC_Body1-R text-[#1F1D1F]">
                                {{ $information['datetime'] ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="50%" valign="top" style="padding-right:26px;">
                    <!-- 2. การเจ็บป่วย -->
                    @foreach ($data as $key => $item)
                        <div class="w-full {{ $loop->first ? '' : 'mt-[26px]' }} flex flex-col items-start gap-[20px]">
                            <div class="w-full p-[10px] bg-[#FBEFF8] keep-together">
                                <label class="PC_Body1-SB text-[#1F1D1F]">
                                    {{ $formKeys[$key] }}
                                </label>
                            </div>

                            <!-- //อุบัติเหตุ (Trauma) -->

                            @foreach ($item as $subItem)
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]"> {{ $subItem['topic'] }} </span>
                                </div>

                                @foreach ($subItem['answers'] as $answer)
                                    <div class="flex flex-col items-start gap-[20px] keep-together">
                                        <div class="page-keep flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $answer }}
                                        </div>
                                    </div>
                                @endforeach
                                @if (isset($subItem['image']))
                                    <button
                                        class="btn_shadow btn_shadow--cancel !flex !justify-center !items-center !h-[50px] !max-w-[90px] !p-0 keep-together">
                                        <span
                                            class="PC_Body2-R whitespace-nowrap leading-none block !text-[13px] text-center">
                                            รูปประกอบ
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endforeach



                    <!-- //บันทึกเพิ่มเติม  -->
                    @if (filled($notes ?? null))
                        <div class="mt-[26px] w-full flex flex-col items-start gap-[20px] keep-together">
                            <div class="page-keep w-full p-[10px] bg-[#FBEFF8]">
                                <label class="PC_Body1-SB text-[#1F1D1F]"> บันทึกเพิ่มเติม</label>
                            </div>

                            <div class="page-keep flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $notes }}
                            </div>
                        </div>
                    @endif

                    <div class="mt-[26px] mb-[26px] w-full border border-[#DFDCDD] border-dashed keep-together">
                    </div>

                    <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ผู้ลงข้อมูล</span>
                    </div>

                    <div class="mt-[20px] flex flex-col items-start gap-[20px] keep-together">
                        {{ $information['created_name'] ?? '-' }}
                    </div>

                </td>

                <td width="50%" valign="top">
                    <div class="w-full border-[1px] border-[#B7B2B5] rounded-[6px] p-4 flex flex-col">
                        <!-- info -->
                        <div class="w-full flex flex-col items-start gap-[20px]">
                            <div class="w-full p-[10px] bg-[#FBEFF8]">
                                <span class="PC_Body1-SB text-[#1F1D1F]">ข้อมูลพื้นฐาน </span>
                            </div>
                            <div
                                class="w-full max-[500px]:flex-col flex items-start max-[500px]:items-center gap-[20px]">
                                <div
                                    class="min-w-[100px] min-h-[100px] w-[100px] h-[100px] border-[1px] border-solid border-[#F2E7F9]">
                                    <img
                                        src="{{ $information['population_image'] ?? 'https://placehold.co/100x100/png' }}">
                                </div>
                                <div class="w-full flex flex-col items-start gap-[24px]">
                                    <div class="w-full flex flex-col gap-[24px]">
                                        <div class="w-full flex flex-col items-start gap-[2px] keep-together">
                                            <div class="PC_Body2-R text-[#7C7379]">ชื่อผู้ประสบภัย</div>
                                            <div class="PC_Body1-R text-[#1F1D1F]">
                                                {{ $information['name_of_assessment'] ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="w-full flex flex-col items-start gap-[2px] keep-together">
                                            <div class="PC_Body2-R text-[#7C7379]">วันเกิด</div>
                                            <div class="PC_Body1-R text-[#1F1D1F]">
                                                {{ $information['birth_date'] }} ( {{ $information['age'] }} ปี)
                                            </div>
                                        </div>

                                        <div class="w-full flex flex-col items-start gap-[2px] keep-together">
                                            <div class="PC_Body2-R text-[#7C7379]">เพศ</div>
                                            <div class="PC_Body1-R text-[#1F1D1F]">
                                                {{ $information['gender'] ?? '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full flex flex-col items-start gap-[2px] keep-together">
                                        <div class="PC_Body2-R text-[#7C7379]">ที่อยู่ตามภูมิลำเนา</div>
                                        <div class="PC_Body1-R text-[#1F1D1F]">
                                            {{ $information['address'] ?? '-' }}
                                        </div>
                                    </div>
                                    @isset($information['pregnancy'])
                                        <div class="w-full flex flex-col items-start gap-[2px] keep-together">
                                            <div class="PC_Body2-R text-[#7C7379]">การตั้งครรภ์</div>
                                            <div class="PC_Body1-R text-[#1F1D1F]">
                                                {{ $information['pregnancy'] ?? '-' }}
                                            </div>
                                        </div>
                                    @endisset
                                </div>
                            </div>

                            @if (filled($information['breastFeeding'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">การให้นมบุตร</span>
                                </div>
                                <div class="page-keep flex items-center">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['breastFeeding']['text'] }}
                                </div>
                            @endif

                            @if (filled($information['headCircumference'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">รอบวงศีรษะ (ซม.)</span>
                                </div>
                                <div class="page-keep flex items-center">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['headCircumference'] }}
                                </div>
                            @endif

                            <!-- ประวัติการรับวัคซีน โรคหัด -->
                            @if (filled($information['measlesVaccinationHistory'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการรับวัคซีน โรคหัด</span>
                                </div>
                                <div class="page-keep flex items-center keep-together">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['measlesVaccinationHistory']['text'] }}
                                </div>
                            @endif

                            <!-- ประวัติการรับวัคซีน บาดทะยัก -->
                            @if (filled($information['historyOfTetanusVaccination'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการรับวัคซีน บาดทะยัก</span>
                                </div>
                                <div class="page-keep flex items-center keep-together">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['historyOfTetanusVaccination']['text'] }}
                                </div>
                            @endif

                            <!-- ประวัติการแพ้ -->
                            @if ($information['historyOfAllergies'])
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการแพ้</span>
                                </div>
                                <div class="page-keep flex flex-col gap-2 keep-together">
                                    <div class="flex items-center keep-together">
                                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                        </div>
                                        {{ $information['historyOfAllergies']['text'] }}
                                    </div>
                                    @if ($information['historyOfAllergies']['value'] == 2)
                                        <div class="w-full flex flex-col gap-2 pl-[28px]">
                                            @if (filled($information['historyOfAllergies']['drug'] ?? null))
                                                <div class="keep-together">
                                                    ยา : {{ $information['historyOfAllergies']['drug'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['historyOfAllergies']['food'] ?? null))
                                                <div class="keep-together">
                                                    อาหาร : {{ $information['historyOfAllergies']['food'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['historyOfAllergies']['other'] ?? null))
                                                <div class="keep-together">
                                                    อื่นๆ : {{ $information['historyOfAllergies']['other'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- ประวัติอดีต -->
                            @if ($information['pastHistory'])
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติอดีต</span>
                                </div>
                                <div class="page-keep flex flex-col gap-2">
                                    <div class="page-keep flex items-center keep-together">
                                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                        </div>
                                        {{ $information['pastHistory']['text'] }}
                                    </div>
                                    @if ($information['pastHistory']['value'] == 2)
                                        <div class="w-full flex flex-col gap-2 pl-[28px]">
                                            @if (filled($information['pastHistory']['ht'] ?? null))
                                                <div class="keep-together">
                                                    HT : {{ $information['pastHistory']['ht'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['pastHistory']['dm'] ?? null))
                                                <div class="keep-together">
                                                    DM : {{ $information['pastHistory']['dm'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['pastHistory']['ba'] ?? null))
                                                <div class="keep-together">
                                                    BA : {{ $information['pastHistory']['ba'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['pastHistory']['other'] ?? null))
                                                <div class="keep-together">
                                                    อื่นๆ : {{ $information['pastHistory']['other'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- ประวัติการใช้ยา -->
                            @if ($information['drugHistory'])
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการใช้ยา</span>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <div class="page-keep flex items-center keep-together">
                                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                        </div>
                                        {{ $information['drugHistory']['text'] }}
                                    </div>
                                    @if ($information['drugHistory']['value'] == 2)
                                        <div class="w-full flex flex-col gap-2 pl-[28px]">
                                            @if (filled($information['drugHistory']['ht'] ?? null))
                                                <div class="keep-together">
                                                    HT : {{ $information['drugHistory']['ht'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['drugHistory']['dm'] ?? null))
                                                <div class="keep-together">
                                                    DM : {{ $information['drugHistory']['dm'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['drugHistory']['ba'] ?? null))
                                                <div class="keep-together">
                                                    BA : {{ $information['drugHistory']['ba'] }}
                                                </div>
                                            @endif
                                            @if (filled($information['drugHistory']['other'] ?? null))
                                                <div class="keep-together">
                                                    อื่นๆ : {{ $information['drugHistory']['other'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- อาการสำคัญ (ซม.) -->
                            @if ($information['importantSymptoms'])
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">อาการสำคัญ</span>
                                </div>
                                <div class="page-keep flex items-center keep-together">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['importantSymptoms'] }}
                                </div>
                            @endif

                            <!-- สัญญาณชีพ -->
                            @if ($information['vitalSigns']['value'])
                                <div class="w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">สัญญาณชีพ</span>
                                </div>
                            @endif

                            <div class="flex flex-col items-start gap-[20px]">
                                @if (filled($information['vitalSigns']['temperature'] ?? null))
                                    <div class="page-keep flex flex-col gap-2 keep-together">
                                        <div class="PC_Body1-SB text-[#1F1D1F]">อุณหภูมิ (องศาเซลเซียส)</div>
                                        <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $information['vitalSigns']['temperature'] }}
                                        </div>
                                    </div>
                                @endif

                                @if (filled($information['vitalSigns']['bloodPressure'] ?? null))
                                    <div class="page-keep flex flex-col gap-2 keep-together">
                                        <div class="PC_Body1-SB text-[#1F1D1F]">
                                            ความดันโลหิต / มิลลิเมตรปรอท
                                        </div>
                                        <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $information['vitalSigns']['bloodPressure'] ?? '-' }} /
                                            {{ $information['vitalSigns']['bloodPressureMillimeterOfMercury'] ?? '-' }}
                                        </div>
                                    </div>
                                @endif

                                @if (filled($information['vitalSigns']['pulse'] ?? null))
                                    <div class="page-keep flex flex-col gap-2 keep-together">
                                        <div class="PC_Body1-SB text-[#1F1D1F]">ชีพจร (ครั้งต่อนาที)</div>
                                        <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $information['vitalSigns']['pulse'] }}
                                        </div>
                                    </div>
                                @endif

                                @if (filled($information['vitalSigns']['respiratoryRate'] ?? null))
                                    <div class="page-keep flex flex-col gap-2 keep-together">
                                        <div class="PC_Body1-SB text-[#1F1D1F]">อัตราหายใจ (ครั้งต่อนาที)</div>
                                        <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $information['vitalSigns']['respiratoryRate'] }}
                                        </div>
                                    </div>
                                @endif

                                @if (filled($information['vitalSigns']['weight'] ?? null))
                                    <div class="page-keep flex flex-col gap-2 keep-together">
                                        <div class="PC_Body1-SB text-[#1F1D1F]">น้ำหนัก (กิโลกรัม)</div>
                                        <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $information['vitalSigns']['weight'] }}
                                        </div>
                                    </div>
                                @endif

                                @if (filled($information['vitalSigns']['height'] ?? null))
                                    <div class="page-keep flex flex-col gap-2 keep-together">
                                        <div class="PC_Body1-SB text-[#1F1D1F]">ส่วนสูง (เซนติเมตร)</div>
                                        <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                            <div
                                                class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                            </div>
                                            {{ $information['vitalSigns']['height'] }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- ประวัติการเจ็บป่วยปัจจุบัน -->
                            @if (filled($information['historyOfCurrentIllness'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการเจ็บป่วยปัจจุบัน</span>
                                </div>
                                <div class="page-keep flex items-center keep-together">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['historyOfCurrentIllness'] }}
                                </div>
                            @endif

                            <!-- การวินิจฉัย -->
                            @if (filled($information['diagnosis'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">การวินิจฉัย</span>
                                </div>
                                <div class="page-keep flex items-center keep-together">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['diagnosis'] }}
                                </div>
                            @endif

                            <!-- ยา ชื่อยา/ขนาดวิธีการใช้ -->
                            @if (filled($information['drugNameDrugSizeHowToUse'] ?? null))
                                <div class="page-keep w-full flex items-center gap-[10px] keep-together">
                                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                                    <span class="PC_Body1-SB text-[#1F1D1F]">ยา ชื่อยา/ขนาดวิธีการใช้</span>
                                </div>
                                <div class="page-keep flex items-center keep-together">
                                    <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf">
                                    </div>
                                    {{ $information['drugNameDrugSizeHowToUse'] }}
                                </div>
                            @endif
                        </div>
                        <!-- info -->
                    </div>

                </td>
            </tr>
        </table>
    </div>
</body>

</html>
