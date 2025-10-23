<?php

namespace App\Constants;

class AssessmentData
{
    public static array $ASSESSMENTS = [
        'sheet_topic' => ['type' => 'topic', 'content' => 'รายงานสรุปผล'],

        'gender_topic'  => ['type' => 'topic', 'content' => 'แยกตามเพศ'],
        'males'  => ['type' => 'text', 'content' => 'ผู้ชาย'],
        'females'  => ['type' => 'text', 'content' => 'ผู้หญิง'],
        'total_genders'  => ['type' => 'total', 'content' => 'รวม'],

        'question_topic'  => ['type' => 'topic', 'content' => 'แยกตามคำถาม'],
        'a_topic' => ['type' => 'topic', 'content' => 'อุบัติเหตุ (Trauma)'],
        'a1'  => ['type' => 'text', 'content' => 'บาดเจ็บบริเวณศีรษะ/กระดูกสันหลังอย่างรุนแรง'],
        'a2'  => ['type' => 'text', 'content' => 'บาดเจ็บบริเวณลำตัวอย่างรุนแรง'],
        'a3'  => ['type' => 'text', 'content' => 'บาดเจ็บบริเวณส่วนปลายแขนขา'],
        'a4'  => ['type' => 'text', 'content' => 'บาดเจ็บปานกลาง'],
        'a5'  => ['type' => 'text', 'content' => 'บาดเจ็บเล็กน้อย/'],

        'b_topic' => ['type' => 'topic', 'content' => 'โรคติดเชื้อ (Infectious disease)'],
        'b1'  => ['type' => 'text', 'content' => 'การติดเชื้อทางเดินหายใจเฉียบพลัน'],
        'b2'  => ['type' => 'text', 'content' => 'ท้องเสียเฉียบพลัน'],
        'b3'  => ['type' => 'text', 'content' => 'ท้องเสียเป็นเลือดเฉียบพลัน'],
        'b4'  => ['type' => 'text', 'content' => 'โรคดีซ่านเฉียบพลัน'],
        'b5'  => ['type' => 'text', 'content' => 'สงสัยเป็นโรคหัด (Measles)'],
        'b6'  => ['type' => 'text', 'content' => 'สงสัยเยื่อหุ้มสมองอักเสบ'],
        'b7'  => ['type' => 'text', 'content' => 'สงสัยบาดทะยัก'],
        'b8'  => ['type' => 'text', 'content' => 'อัมพาตเฉียบพลัน'],
        'b9'  => ['type' => 'text', 'content' => 'ไข้เลือดออกเฉียบพลัน'],
        'b10'  => ['type' => 'text', 'content' => 'ไข้ไม่ทราบสาเหตุ'],

        'c_topic' => ['type' => 'topic', 'content' => 'โรคฉุกเฉิน'],
        'c1'  => ['type' => 'text', 'content' => 'โรคทางศัลยกรรมฉุกเฉิน (ไม่ใช่การบาดเจ็บ)'],
        'c2'  => ['type' => 'text', 'content' => 'โรคทางอายุรกรรมฉุกเฉิน (ไม่ติดเชื้อ)'],

        'd_topic' => ['type' => 'topic', 'content' => 'โรคสำคัญอื่นๆ'],
        'd1'  => ['type' => 'text', 'content' => 'โรคผิวหนัง'],
        'd2'  => ['type' => 'text', 'content' => 'ปัญหาสุขภาพจิตเฉียบพลัน'],
        'd3'  => ['type' => 'text', 'content' => 'ภาวะแทรกซ้อนทางสูติกรรม'],
        'd4'  => ['type' => 'text', 'content' => 'ภาวะทุพโภชนาการเฉียบพลันรุนแรง (SAM)'],
        'd5'  => ['type' => 'text', 'content' => 'การวินิจฉัยอื่น ๆ ที่ไม่ได้ระบุไว้ข้างต้น'],

        'e_topic' => ['type' => 'topic', 'content' => 'หัตถการ'],
        'e1'  => ['type' => 'text', 'content' => 'ฉีดยา'],
        'e2'  => ['type' => 'text', 'content' => 'ทำแผล'],
        'e3'  => ['type' => 'text', 'content' => 'ทำแผล'],

        'f_topic' => ['type' => 'topic', 'content' => 'ผลการรักษา'],
        'f1'  => ['type' => 'text', 'content' => 'Discharge (OPD)'],
        'f2'  => ['type' => 'text', 'content' => 'Admit (IPD)'],
        'f3'  => ['type' => 'text', 'content' => 'Refer'],

        'total_questions'  => ['type' => 'total', 'content' => 'รวม'],
    ];

    public static array $VISITS = [
        'sheet_topic' => ['type' => 'topic', 'content' => 'รายงานสรุปผล'],

        'gender_topic'  => ['type' => 'topic', 'content' => 'แยกตามเพศ'],
        'males'  => ['type' => 'text', 'content' => 'ผู้ชาย'],
        'females'  => ['type' => 'text', 'content' => 'ผู้หญิง'],
        'total_genders'  => ['type' => 'total', 'content' => 'รวม'],

        'question_topic'  => ['type' => 'topic', 'content' => 'แยกตามคำถาม'],
        'detail_topic' => ['type' => 'topic', 'content' => 'ข้อมูลพิ้นฐาน'],
        'breast_feeding_topic' => ['type' => 'topic', 'content' => 'การให้นมบุตร'],
        'breast_feeding_0'  => ['type' => 'text', 'content' => 'ไม่ทราบ'],
        'breast_feeding_1'  => ['type' => 'text', 'content' => 'ใช่'],
        'breast_feeding_2'  => ['type' => 'text', 'content' => 'ไม่ใช่'],

        'measles_vaccination_topic' => ['type' => 'topic', 'content' => 'ประวัติการรับวัคซีน โรคหัด'],
        'measles_vaccination_history_0'  => ['type' => 'text', 'content' => 'ไม่ทราบ'],
        'measles_vaccination_history_1'  => ['type' => 'text', 'content' => 'ใช่'],
        'measles_vaccination_history_2'  => ['type' => 'text', 'content' => 'ไม่ใช่'],

        'tetanus_vaccination_topic' => ['type' => 'topic', 'content' => 'ประวัติการรับวัคซีน บาดทะยัก'],
        'history_of_tetanus_vaccination_0'  => ['type' => 'text', 'content' => 'ไม่ทราบ'],
        'history_of_tetanus_vaccination_1'  => ['type' => 'text', 'content' => 'ใช่'],
        'history_of_tetanus_vaccination_2'  => ['type' => 'text', 'content' => 'ไม่ใช่'],

        'allergies_topic' => ['type' => 'topic', 'content' => 'ประวัติการแพ้'],
        'history_of_allergies_0'  => ['type' => 'text', 'content' => 'ไม่ทราบ'],
        'history_of_allergies_1'  => ['type' => 'text', 'content' => 'ไม่มี'],
        'history_of_allergies_2'  => ['type' => 'text', 'content' => 'มี'],

        'past_history_topic' => ['type' => 'topic', 'content' => 'ประวัติอดีต'],
        'past_history_0'  => ['type' => 'text', 'content' => 'ไม่ทราบ'],
        'past_history_1'  => ['type' => 'text', 'content' => 'ไม่มี'],
        'past_history_2'  => ['type' => 'text', 'content' => 'มี'],

        'drug_history_topic' => ['type' => 'topic', 'content' => 'ประวัติการใช้ยา'],
        'drug_history_0'  => ['type' => 'text', 'content' => 'ไม่ทราบ'],
        'drug_history_1'  => ['type' => 'text', 'content' => 'ไม่มี'],
        'drug_history_2'  => ['type' => 'text', 'content' => 'มี'],

        'weight_topic' => ['type' => 'topic', 'content' => 'น้ำหนัก (กิโลกรัม)'],
        'weight_1_40'  => ['type' => 'text', 'content' => '1-40'],
        'weight_41_50'  => ['type' => 'text', 'content' => '41-50'],
        'weight_51_60'  => ['type' => 'text', 'content' => '51-60'],
        'weight_61_70'  => ['type' => 'text', 'content' => '61-70'],
        'weight_71_80'  => ['type' => 'text', 'content' => '71-80'],
        'weight_over_80'  => ['type' => 'text', 'content' => '80+'],

        'height_topic' => ['type' => 'topic', 'content' => 'ส่วนสูง (เซนติเมตร)'],
        'height_1_100'  => ['type' => 'text', 'content' => '1-100'],
        'height_101_150'  => ['type' => 'text', 'content' => '101-150'],
        'height_151_160'  => ['type' => 'text', 'content' => '151-160'],
        'height_161_170'  => ['type' => 'text', 'content' => '161-170'],
        'height_171_180'  => ['type' => 'text', 'content' => '171-180'],
        'height_over_80'  => ['type' => 'text', 'content' => '180+'],
        
        'illness_a_topic' => ['type' => 'topic', 'content' => 'การเจ็บป่วย'],
        'illness_a1_topic' => ['type' => 'topic', 'content' => 'อุบัติเหตุ (Trauma)'],
        'illness_a1_0'  => ['type' => 'text', 'content' => 'บาดเจ็บบริเวณศีรษะ/กระดูกสันหลังอย่างรุนแรง'],
        'illness_a1_1'  => ['type' => 'text', 'content' => 'บาดเจ็บบริเวณลำตัวอย่างรุนแรง'],
        'illness_a1_2'  => ['type' => 'text', 'content' => 'บาดเจ็บบริเวณส่วนปลายแขนขา'],
        'illness_a1_3'  => ['type' => 'text', 'content' => 'บาดเจ็บปานกลาง'],
        'illness_a1_4'  => ['type' => 'text', 'content' => 'บาดเจ็บเล็กน้อย'],

        'illness_a2_topic' => ['type' => 'topic', 'content' => 'โรคติดเชื้อ (Infectious disease)'],
        'illness_a2_0'  => ['type' => 'text', 'content' => 'การติดเชื้อทางเดินหายใจเฉียบพลัน'],
        'illness_a2_1'  => ['type' => 'text', 'content' => 'ท้องเสียเฉียบพลัน'],
        'illness_a2_2'  => ['type' => 'text', 'content' => 'ท้องเสียเป็นเลือดเฉียบพลัน'],
        'illness_a2_3'  => ['type' => 'text', 'content' => 'โรคดีซ่านเฉียบพลัน'],
        'illness_a2_4'  => ['type' => 'text', 'content' => 'สงสัยเป็นโรคหัด (Measles)'],
        'illness_a2_5'  => ['type' => 'text', 'content' => 'สงสัยเยื่อหุ้มสมองอักเสบ'],
        'illness_a2_6'  => ['type' => 'text', 'content' => 'สงสัยบาดทะยัก'],
        'illness_a2_7'  => ['type' => 'text', 'content' => 'อัมพาตเฉียบพลัน'],
        'illness_a2_8'  => ['type' => 'text', 'content' => 'ไข้เลือดออกเฉียบพลัน'],
        'illness_a2_9'  => ['type' => 'text', 'content' => 'ไข้ไม่ทราบสาเหตุ'],

        'illness_a3_topic' => ['type' => 'topic', 'content' => 'โรคฉุกเฉิน'],
        'illness_a3_0'  => ['type' => 'text', 'content' => 'โรคทางศัลยกรรมฉุกเฉิน (ไม่ใช่การบาดเจ็บ)'],
        'illness_a3_1'  => ['type' => 'text', 'content' => 'โรคทางอายุรกรรมฉุกเฉิน (ไม่ติดเชื้อ)'],

        'illness_a4_topic' => ['type' => 'topic', 'content' => 'โรคสำคัญอื่นๆ'],
        'illness_a4_0'  => ['type' => 'text', 'content' => 'โรคผิวหนัง'],
        'illness_a4_1'  => ['type' => 'text', 'content' => 'ปัญหาสุขภาพจิตเฉียบพลัน'],
        'illness_a4_2'  => ['type' => 'text', 'content' => 'ภาวะแทรกซ้อนทางสูติกรรม'],
        'illness_a4_3'  => ['type' => 'text', 'content' => 'ภาวะทุพโภชนาการเฉียบพลันรุนแรง (SAM) *'],
        'illness_a4_4'  => ['type' => 'text', 'content' => 'การวินิจฉัยอื่น ๆ ที่ไม่ได้ระบุไว้ข้างต้น'],

        'care_b_topic' => ['type' => 'topic', 'content' => 'หัตถการและผลการรักษา'],
        'care_b1_topic' => ['type' => 'topic', 'content' => 'หัตถการ'],
        'care_b1_0'  => ['type' => 'text', 'content' => 'หัตถการหลัก (Major procedure) (ไม่รวม MDS31)'],
        'care_b1_1'  => ['type' => 'text', 'content' => 'การตัดแขนขาไม่รวมนิ้ว *'],
        'care_b1_2'  => ['type' => 'text', 'content' => 'การผ่าตัดเล็ก'],
        'care_b1_3'  => ['type' => 'text', 'content' => 'การคลอดปกติผ่านช่องคลอด (NVD)'],
        'care_b1_4'  => ['type' => 'text', 'content' => 'การผ่าคลอด'],
        'care_b1_5'  => ['type' => 'text', 'content' => 'สูติศาสตร์ อื่นๆ'],

        'care_b2_topic' => ['type' => 'topic', 'content' => 'ผลการรักษา'],
        'care_b2_0'  => ['type' => 'text', 'content' => 'ออกจากโรงพยาบาลโดยไม่มีการติดตามการรักษาพยาบาล'],
        'care_b2_1'  => ['type' => 'text', 'content' => 'ออกจากโรงพยาบาลพร้อมติดตามการรักษา'],
        'care_b2_2'  => ['type' => 'text', 'content' => 'การปฏิเสธการรักษา'],
        'care_b2_3'  => ['type' => 'text', 'content' => 'ส่งต่อไปยังสถานพยาบาลอื่น'],
        'care_b2_4'  => ['type' => 'text', 'content' => 'รับไว้เป็นผู้ป่วยใน'],
        'care_b2_5'  => ['type' => 'text', 'content' => 'เสียชีวิตตั้งแต่มาถึง'],
        'care_b2_6'  => ['type' => 'text', 'content' => 'เสียชีวิตภายในสถานพยาบาล *'],
        'care_b2_7'  => ['type' => 'text', 'content' => 'ต้องการการฟื้นฟูระยะยาว *'],

        'context_c_topic' => ['type' => 'topic', 'content' => 'บริบท'],
        'context_c1_topic' => ['type' => 'topic', 'content' => 'ความสัมพันธ์'],
        'context_c1_0' => ['type' => 'text', 'content' => 'เกี่ยวข้องกับสาธารณภัยครั้งนี้โดยตรง'],
        'context_c1_1' => ['type' => 'text', 'content' => 'เกี่ยวข้องกับสาธารณภัยครั้งนี้ทางอ้อม'],
        'context_c1_2' => ['type' => 'text', 'content' => 'ไม่เกี่ยวข้องกับสาธารณภัยครั้งนี้'],
        'context_c2_topic' => ['type' => 'topic', 'content' => 'การป้องกัน'],
        'context_c2_0' => ['type' => 'text', 'content' => 'เด็กเปราะบาง *'],
        'context_c2_1' => ['type' => 'text', 'content' => 'ผู้ใหญ่เปราะบาง *'],
        'context_c2_2' => ['type' => 'text', 'content' => 'ความรุนแรงทางเพศทางเพศ (SGBV) *'],
        'context_c2_3' => ['type' => 'text', 'content' => 'ความรุนแรง (ไม่ใช่ SGBV) *'],

        'total_questions'  => ['type' => 'total', 'content' => 'รวม'],
    ];
}
