<?php
// https://github.com/navysingchai/program_documentation
class Program
{
    public $MonthNameTH = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");

    public function UploadFile($File, $Path, $AllowedExtensions, $ReferenceName)
    {
        if (!is_dir($Path)) {
            echo "<pre>";
            print_r([
                'status' => 'error',
                'type' => 'path',
                'msg' => 'not found path'
            ]);
            exit;
        }
        if (is_null($AllowedExtensions)) {
            $AllowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        }
        $FileTmp = $File['tmp_name']; //tmp file
        $FileError = $File['error']; // error file
        $FileExt = strtolower(pathinfo($File['name'], PATHINFO_EXTENSION)); //file type
        if (in_array($FileExt, $AllowedExtensions)) {
            if ($FileError === 0) {
                $FileName = "{$ReferenceName}.{$FileExt}";
                if (move_uploaded_file($FileTmp, "{$Path}{$FileName}")) {
                    return [
                        'status' => 'success',
                        'filename' => $FileName
                    ];
                } else {
                    echo "<pre>";
                    print_r([
                        'status' => 'error',
                        'type' => "upload",
                        'msg' => "upload"
                    ]);
                    exit;
                }
            } else {
                echo "<pre>";
                print_r([
                    'status' => 'error',
                    'type' => 'fileErr',
                    'msg' => 'fileErr'
                ]);
                exit;
            }
        } else {
            echo "<pre>";
            print_r([
                'status' => 'error',
                'type' => 'fileType',
                'msg' => 'fileType'
            ]);
            exit;
        }
    }

    public function SetAccessCheckRedirectPath($RedirectPath)
    {
        $MM_qsChar = "?";
        $MM_referrer = $_SERVER['PHP_SELF'];
        if (strpos($RedirectPath, "?")) {
            $MM_qsChar = "&";
        }
        if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
            $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
        }
        $RedirectPath = $RedirectPath . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
        return $RedirectPath;
    }

    public function XssProtection($var)
    {
        // แยกการตรวจสอบก่อนใช้งาน
        $trimmedVar = trim($var);
        return isset($var) && !empty($trimmedVar) ? htmlspecialchars($trimmedVar, ENT_QUOTES, "UTF-8") : null;
    }

    public function MD5Token()
    {
        return md5(uniqid() . mt_rand());
    }

    public function ConvertDate($var, $DefaultDateType, $ConvertType)
    {
        if ($DefaultDateType == "Y-m-d" && $ConvertType == "d/m/Y") {
            $var = explode("-", $var);
            return "{$var[2]}/{$var[1]}/{$var[0]}";
        }
        if ($DefaultDateType == "d/m/Y" && $ConvertType == "Y-m-d") {
            $var = explode("/", $var);
            return "{$var[2]}-{$var[1]}-{$var[0]}";
        }
        return $var;
    }

    public function DateThai($Date, $DateType, $ResultMonthType = "ShortMonth", $ResultYearType = "ShortYear")
    {
        if ($DateType == "Y-m-d") {
            $Date = explode("-", $Date);
        } elseif ($DateType == "d/m/Y") {
            $Date = explode("/", $Date);
        } else {
            return "Invalid DateType";
        }
        if (count($Date) != 3) {
            return "Invalid Date";
        }

        // แยกวันที่ เดือน และปี
        if ($DateType == "Y-m-d") {
            $year = $Date[0];
            $month = $Date[1];
            $day = $Date[2];
        } elseif ($DateType == "d/m/Y") {
            $day = $Date[0];
            $month = $Date[1];
            $year = $Date[2];
        }

        if ($ResultYearType == "ShortYear") {
            $thaiYear = $year + 543;
            $thaiYear = substr($thaiYear, -2);
        } elseif ($ResultYearType == "FullYear") {
            $thaiYear = $year + 543;
        } else {
            return "Invalid ResultYearType";
        }

        $thaiMonths = [
            "01" => ["มกราคม", "ม.ค."],
            "02" => ["กุมภาพันธ์", "ก.พ."],
            "03" => ["มีนาคม", "มี.ค."],
            "04" => ["เมษายน", "เม.ย."],
            "05" => ["พฤษภาคม", "พ.ค."],
            "06" => ["มิถุนายน", "มิ.ย."],
            "07" => ["กรกฎาคม", "ก.ค."],
            "08" => ["สิงหาคม", "ส.ค."],
            "09" => ["กันยายน", "ก.ย."],
            "10" => ["ตุลาคม", "ต.ค."],
            "11" => ["พฤศจิกายน", "พ.ย."],
            "12" => ["ธันวาคม", "ธ.ค."]
        ];

        if ($ResultMonthType == "ShortMonth") {
            $thaiMonth = $thaiMonths[$month][1];
            return "{$day} {$thaiMonth} {$thaiYear}";
        } elseif ($ResultMonthType == "FullMonth") {
            $thaiMonth = $thaiMonths[$month][0];
            return "{$day} {$thaiMonth} {$thaiYear}";
        } else {
            return "Invalid ResultType";
        }
    }
    public function IsNotEmpty($var)
    {
        $var = trim($var);
        if (empty($var)) {
            return false;
        } else {
            return true;
        }
    }

    public function array_validation($var, $key, $order = null) {
        if(is_null($order)) {
            $val = is_array($var) && array_key_exists($key, $var) ? $var[$key] : null;
        }else {
            $val = is_array($var) && array_key_exists($key, $var) && isset($var[$key][$order]) ? $var[$key][$order] : null;
        }
        if(!is_null($val)) {
            $val = trim($val);
            if(!empty($val)) {
                return htmlspecialchars($val, ENT_QUOTES, "UTF-8");
            }
        }
        return null;
    }

    public function arr_valid($var, $key, $order = null) {
        if(is_null($order)) {
            $val = is_array($var) && array_key_exists($key, $var) ? $var[$key] : null;
        }else {
            $val = is_array($var) && array_key_exists($key, $var) && isset($var[$key][$order]) ? $var[$key][$order] : null;
        }
        if(!is_null($val)) {
            $val = trim($val);
            if(!empty($val)) {
                return htmlspecialchars($val, ENT_QUOTES, "UTF-8");
            }
        }
        return null;
    }

    public function redirect_validation($path_to_redirect, $var, $value) {
        if($var == $value) {
            header("location: {$path_to_redirect}");
            exit;
        }
    } 

    public function redirect($path_to_redirect) {
        header("location: {$path_to_redirect}");
        exit;
    } 

    public function get_header_json_string() {
        return "Content-type: application/json; charset=utf-8";
    }
}
