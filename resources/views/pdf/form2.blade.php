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
                            <div class="PC_Body1-R text-[#1F1D1F]">{{$information['count']}}</div>
                        </div>
                        <div class="h-[47px] border-r-1 border-[#DFDCDD]"></div>
                        <div class="flex flex-col">
                            <div class="PC_Body2-R text-[#7C7379]">วันที่ประเมิน</div>
                            <div class="PC_Body1-R text-[#1F1D1F]">
                                {{$information['created_at']}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col-reverse sm:flex-row gap-[26px] items-start flex-wrap sm:flex-nowrap" style="display: flex; width: 100%;">
            <div class="w-full flex flex-col" style="flex: 1; width: 50%;">
                <!-- 2. การเจ็บป่วย -->

                @foreach($data as $item)

                <div class="w-full {{ $loop->first ? '' : 'mt-[26px]' }} flex flex-col items-start gap-[20px]">
                    <div class="w-full p-[10px] bg-[#FBEFF8]" style="page-break-inside: avoid;">
                        <label class="PC_Body1-SB text-[#1F1D1F]">
                            {{$item['topic']}}
                        </label>
                    </div>

                    <!-- //อุบัติเหตุ (Trauma) -->

                    @foreach($item['data'] as $subData)
                    <div class="page-keep w-full flex items-center gap-[10px]" style="page-break-inside: avoid;">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]"> {{$subData['title']}} </span>
                    </div>

                    @foreach($subData['questions'] as $question)
                    <div class="flex flex-col items-start gap-[20px]" style="page-break-inside: avoid;">
                        <div class="page-keep flex items-center">
                            <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                            {{$question}}
                        </div>
                    </div>
                    @endforeach
                    @endforeach
                </div>
                @endforeach



                <!-- //บันทึกเพิ่มเติม  -->
                 @if($notes)
                <div class="mt-[26px] w-full flex flex-col items-start gap-[20px]" style="page-break-inside: avoid;">
                    <div class="page-keep w-full p-[10px] bg-[#FBEFF8]">
                        <label class="PC_Body1-SB text-[#1F1D1F]"> บันทึกเพิ่มเติม</label>
                    </div>

                    <div class="page-keep flex items-center mb-[26px]">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $notes }}
                    </div>
                </div>
                @endif

                <div class="mb-[26px] w-full border border-[#DFDCDD] border-dashed" style="page-break-inside: avoid;">
                </div>

                <div class="page-keep w-full flex items-center gap-[10px]" style="page-break-inside: avoid;">
                    <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                    <span class="PC_Body1-SB text-[#1F1D1F]">ผู้ลงข้อมูล</span>
                </div>

                <div class="mt-[20px] flex flex-col items-start gap-[20px]" style="page-break-inside: avoid;">
                    {{ $information['created_name'] ?? "-" }}
                </div>
            </div>

            <div class="w-full border-[1px] border-[#B7B2B5] rounded-[6px] p-4 flex flex-col" style="flex: 1; width: 50%;">
                <!-- info -->

                <div class="w-full flex flex-col items-start gap-[20px]">
                    <div class="w-full p-[10px] bg-[#FBEFF8]">
                        <span class="PC_Body1-SB text-[#1F1D1F]">ข้อมูลพื้นฐาน </span>
                    </div>
                    <div class="w-full max-[500px]:flex-col flex items-start max-[500px]:items-center gap-[20px]">
                        <div class="min-w-[100px] min-h-[100px] w-[100px] h-[100px] border-[1px] border-solid border-[#F2E7F9]">
                            <img src='https://placehold.co/100x100/png' alt="" />
                        </div>
                        <div class="w-full flex flex-col items-start gap-[24px]">
                            <div class="w-full flex flex-col gap-[24px]">
                                <div class="w-full flex flex-col items-start gap-[2px]">
                                    <div class="PC_Body2-R text-[#7C7379]">ชื่อผู้ประสบภัย</div>
                                    <div class="PC_Body1-R text-[#1F1D1F]">
                                        {{ $information['name_of_assessment'] ?? "-" }}
                                    </div>
                                </div>

                                <div class="w-full flex flex-col items-start gap-[2px]">
                                    <div class="PC_Body2-R text-[#7C7379]">วันเกิด</div>
                                    <div class="PC_Body1-R text-[#1F1D1F]">
                                        {{ $information['birth_date']}} ( {{$information['age'] }} ปี)
                                    </div>
                                </div>

                                <div class="w-full flex flex-col items-start gap-[2px]">
                                    <div class="PC_Body2-R text-[#7C7379]">เพศ</div>
                                    <div class="PC_Body1-R text-[#1F1D1F]">
                                        {{ $information['gender'] ?? "-" }}
                                    </div>
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-start gap-[2px]">
                                <div class="PC_Body2-R text-[#7C7379]">ที่อยู่ตามภูมิลำเนา</div>
                                <div class="PC_Body1-R text-[#1F1D1F]">
                                    {{ $information['address'] ?? "-" }}
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-start gap-[2px]">
                                <div class="PC_Body2-R text-[#7C7379]">การตั้งครรภ์</div>
                                <div class="PC_Body1-R text-[#1F1D1F]">
                                    {{ $information['pregnancy'] ?? "-" }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">การให้นมบุตร</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $information['breastFeeding']['text'] }}
                    </div>

                    @if($information['headCircumference'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">รอบวงศีรษะ (ซม.)</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>

                    </div>
                    @endif

                    <!-- ประวัติการรับวัคซีน โรคหัด -->
                     @if($information['measlesVaccinationHistory'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการรับวัคซีน โรคหัด</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $information['measlesVaccinationHistory']['text'] }}
                    </div>
                    @endif

                    <!-- ประวัติการรับวัคซีน บาดทะยัก -->
                    @if($information['historyOfTetanusVaccination'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการรับวัคซีน บาดทะยัก</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $information['historyOfTetanusVaccination']['text'] }}
                    </div>
                    @endif

                    <!-- ประวัติการแพ้ -->
                    @if($information['historyOfAllergies'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการแพ้</span>
                    </div>
                    <div class="page-keep flex flex-col gap-2">
                        <div class="flex items-center">
                            <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                            {{ $information['historyOfAllergies']['text'] }}
                        </div>
                         @if($information['historyOfAllergies']['value'] == 2)
                        <div class="w-full flex flex-col gap-2 pl-[28px]">
                            <div class="">
                                ยา : {{ $information['historyOfAllergies']['drug'] }}
                            </div>
                            <div class="">
                                อาหาร : {{ $information['historyOfAllergies']['food'] }}
                            </div>
                            <div class="">
                                อื่นๆ : {{ $information['historyOfAllergies']['other'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- ประวัติอดีต -->
                    @if($information['pastHistory'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติอดีต</span>
                    </div>
                    <div class="page-keep flex flex-col gap-2">
                        <div class="page-keep flex items-center">
                            <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                             {{ $information['pastHistory']['text'] }}
                        </div>
                        @if($information['historyOfAllergies']['value'] == 2)
                        <div class="w-full flex flex-col gap-2 pl-[28px]">
                            <div class="">
                                HT :  {{ $information['pastHistory']['ht'] }}
                            </div>
                            <div class="">
                                DM :  {{ $information['pastHistory']['dm'] }}
                            </div>
                            <div class="">
                                BA :  {{ $information['pastHistory']['ba'] }}
                            </div>
                            <div class="">
                                อื่นๆ :  {{ $information['pastHistory']['other'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- ประวัติการใช้ยา -->
                    @if($information['drugHistory'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการใช้ยา</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="page-keep flex items-center">
                            <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                            {{ $information['drugHistory']['text'] }}
                        </div>
                        @if($information['historyOfAllergies']['value'] == 2)
                        <div class="w-full flex flex-col gap-2 pl-[28px]">
                            <div class="">
                                HT : {{ $information['drugHistory']['ht'] }}
                            </div>
                            <div class="">
                                DM : {{ $information['drugHistory']['dm'] }}
                            </div>
                            <div class="">
                                BA : {{ $information['drugHistory']['ba'] }}
                            </div>
                            <div class="">
                                อื่นๆ : {{ $information['drugHistory']['other'] }}
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- อาการสำคัญ (ซม.) -->
                    @if($information['importantSymptoms'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">อาการสำคัญ</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $information['importantSymptoms'] }}
                    </div>
                    @endif

                    <!-- สัญญาณชีพ -->
                    @if($information['vitalSigns']['value'])
                    <div class="w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">สัญญาณชีพ</span>
                    </div>
                    @endif

                    <div class="flex flex-col items-start gap-[20px]">
                        @if($information['vitalSigns']['temperature'])
                        <div class="page-keep flex flex-col gap-2">
                            <div class="PC_Body1-SB text-[#1F1D1F]">อุณหภูมิ (องศาเซลเซียส)</div>
                            <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $information['vitalSigns']['temperature'] }}
                            </div>
                        </div>
                        @endif

                        @if($information['vitalSigns']['bloodPressure'])
                        <div class="page-keep flex flex-col gap-2">
                            <div class="PC_Body1-SB text-[#1F1D1F]">
                                ความดันโลหิต / มิลลิเมตรปรอท
                            </div>
                            <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $information['vitalSigns']['bloodPressure'] ?? "-" }} / {{ $information['vitalSigns']['bloodPressureMillimeterOfMercury'] ?? "-" }}
                            </div>
                        </div>
                        @endif

                        @if($information['vitalSigns']['pulse'])
                        <div class="page-keep flex flex-col gap-2">
                            <div class="PC_Body1-SB text-[#1F1D1F]">ชีพจร (ครั้งต่อนาที)</div>
                            <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $information['vitalSigns']['pulse'] }}
                            </div>
                        </div>
                        @endif

                        @if($information['vitalSigns']['respiratoryRate'])
                        <div class="page-keep flex flex-col gap-2">
                            <div class="PC_Body1-SB text-[#1F1D1F]">อัตราหายใจ (ครั้งต่อนาที)</div>
                            <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $information['vitalSigns']['respiratoryRate'] }}
                            </div>
                        </div>
                        @endif

                        @if($information['vitalSigns']['weight'])
                        <div class="page-keep flex flex-col gap-2">
                            <div class="PC_Body1-SB text-[#1F1D1F]">น้ำหนัก (กิโลกรัม)</div>
                            <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $information['vitalSigns']['weight'] }}
                            </div>
                        </div>
                        @endif

                        @if($information['vitalSigns']['height'])
                        <div class="page-keep flex flex-col gap-2">
                            <div class="PC_Body1-SB text-[#1F1D1F]">ส่วนสูง (เซนติเมตร)</div>
                            <div class="PC_Body2-R text-[#1F1D1F] flex items-center">
                                <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                                {{ $information['vitalSigns']['height'] }}
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- ประวัติการเจ็บป่วยปัจจุบัน -->
                    @if($information['historyOfCurrentIllness'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ประวัติการเจ็บป่วยปัจจุบัน</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $information['historyOfCurrentIllness'] }}
                    </div>
                    @endif

                    <!-- การวินิจฉัย -->
                    @if($information['diagnosis'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">การวินิจฉัย</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{ $information['diagnosis'] }}
                    </div>
                    @endif

                    <!-- ยา ชื่อยา/ขนาดวิธีการใช้ -->
                     @if($information['drugNameDrugSizeHowToUse'])
                    <div class="page-keep w-full flex items-center gap-[10px]">
                        <div class="w-[4px] h-[30px] bg-[#EECCE7]"></div>
                        <span class="PC_Body1-SB text-[#1F1D1F]">ยา ชื่อยา/ขนาดวิธีการใช้</span>
                    </div>
                    <div class="page-keep flex items-center">
                        <div class="w-[4px] h-[4px] bg-[#A8A3A6] rounded-full ml-2.5 mr-3.5 mt-20-pdf"></div>
                        {{$information['drugNameDrugSizeHowToUse']}}
                    </div>
                    @endif
                </div>
                <!-- info -->
            </div>
        </div>
    </div>
</body>

</html>
