<?php

namespace App\Http\Utility;

use App\Models\Geography;

class AddressUtility
{
    public static function getFullAddressTH($address, ?Geography $geography, $postalCode = true)
    {
        $fullAdress = $address;
        $postalCode = $postalCode ? $geography->postal_code : "";
        if ($geography) {
            if ($geography->province_code === 10) {
                $fullAdress .= ' แขวง ' . $geography->subdistrict_name_th . ' เขต ' . $geography->district_name_th . ' จ.' . $geography->province_name_th. ' ' . $postalCode;
            } else {
                $fullAdress .= ' ต.' . $geography->subdistrict_name_th . ' อ.' . $geography->district_name_th . ' จ.' . $geography->province_name_th . ' ' . $postalCode;
            }
        }

        return $fullAdress;
    }

    public static function getFullAddressEN($address, ?Geography $geography, $postalCode = true)
    {
        $fullAdress = $address;
        $postalCode = $postalCode ? $geography->postal_code : "";
        if ($geography) {
            $fullAdress .= ' Tambon' . $geography->subdistrict_name_en . ' Amphoe' . $geography->district_name_en . ' ' . $geography->province_name_en . ' ' . $postalCode;
        }
        return $fullAdress;
    }
}
