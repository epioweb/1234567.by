<?
/*
 * Чтение csv-файлов в массив
 * с установкой ограничений на количество отдаваемых строк
 *
 * Файлы для обработки должны следующего формата
 * "АВИА-МОРЕ";"АВИА-МОРЕ КАРАМЕЛЬ ГОМЕОПАТИЧЕСКАЯ 5; Г N15";20000054;2001812;2001812;"МАТЕРИА МЕДИКА";"МАТЕРИА МЕДИКА"
 * Текст в полях заключен в кавычки, поля разделены символом ";"
 *
 */
class CReadCsv
{
    function CReadCsv($fromEncoding = "", $toEncoding = "")
    {
        $this->fileEncoding = strlen($fromEncoding) > 0 ? $fromEncoding : "UTF-8";
        $this->dbEncoding = strlen($toEncoding) > 0 ? $toEncoding : "UTF-8";
    }

    function Read($file, $step = 0, $line = 0)
    {
        $line = (int) $line;
        $step = (int) $step;
        $topMargin = $step + $line;

        $arFields = array();
        $lastLine = 0;
        $indexPos = 0;

        //$this->Log($file);
        if(file_exists($file))
        {
            if (($handle = fopen($file, "r")) !== FALSE)
            {
                //$this->Log("ok");
                $indexPos = 0;
                while (($data = fgetcsv($handle, 10000, ";", "\"")) !== FALSE)
                {
                    $data = $this->ConvertArrEncoding( $this->fileEncoding, $this->dbEncoding, $data);
                    if($step > 0)
                    {
                        if($indexPos >= $topMargin)
                            break;

                        if(($indexPos <= $topMargin) && ($indexPos >= $line))
                            $arFields[] = $data;
                    }
                    else
                        $arFields[] = $data;

                    $indexPos++;
                }
                $line = $indexPos;

                fclose($handle);
            }
            else
                $this->Log( "can`t open file" );
        }
        else
            $this->Log( "file not exists" );

        return $arFields;
    }

    private function ConvertArrEncoding( $from, $to, $arr)
    {
        if( empty($from) )
            $from = $this->fileEncoding;
        if( empty($to) )
            $to = $this->dbEncoding;

        if( is_array($arr) && $from != $to )
        {
            foreach( $arr as $key=>&$val )
            {
                if( strlen($val) > 0 )
                {
                    $val = iconv( $from, $to, $val);
                    $val = str_replace("|", "", $val);
                }
            }
        }
        return $arr;
    }
}
?>