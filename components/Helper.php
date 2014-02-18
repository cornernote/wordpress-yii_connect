<?php
/**
 *
 */
class Helper
{

    /**
     * @static
     * @param $id
     */
    public static function searchToggle($id)
    {
        $showSearch = sf('showSearch') ? "$('.search-button').click();" : '';
        cs()->registerScript($id . '-search', "
            $(document).ready(function(){
                $('.search-button').click(function(){
                    $('.search-form').toggle();
                });
                $showSearch
                $('.search-form form').submit(function(){
                    $.fn.yiiGridView.update('$id', {
                        url: $(this).attr('action'),
                        data: $(this).serialize()
                    });
                    return false;
                });
            });
        ");
    }

    /**
     * @static
     * @return bool
     */
    public static function isMobileBrowser()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $userAgent)
            || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))
        )
            return true;
        return false;
    }

    /**
     * @param $dir
     * @param $removeSelf
     */
    public static function removeDirectory($dir, $removeSelf = true)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        self::removeDirectory($dir . "/" . $object, true);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            if ($removeSelf) {
                rmdir($dir);
            }
        }
    }

    /**
     * @param $table
     * @return bool
     */
    public static function tableExists($table)
    {
        return (Yii::app()->getDb()->createCommand("SHOW TABLES LIKE '" . $table . "'")->queryScalar() == $table);
    }

    /**
     * @param $source
     * @param $destination
     * @return bool
     */
    public static function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', realpath($file));

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

    /**
     * @param string $dir
     * @param int $mode
     * @param bool $recursive
     * @return string
     * @throws CException
     */
    public static function createDirectory($dir, $mode = 0777, $recursive = true)
    {
        if (file_exists($dir)) {
            return $dir;
        }
        $created = @mkdir($dir, $mode, $recursive);
        if (!$created) {
            //another script did not create the directory
            if (!file_exists($dir)) {
                throw new CException('Error occurred when trying to create directory ' . $dir);
            }
        }
        return $dir;
    }

    /**
     * @param $email
     * @return string
     */
    public static function hideEmail($email)
    {
        $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
        $key = str_shuffle($character_set);
        $cipher_text = '';
        $id = 'e' . rand(1, 999999999);
        for ($i = 0; $i < strlen($email); $i += 1) $cipher_text .= $key[strpos($character_set, $email[$i])];
        $script = 'var a="' . $key . '";var b=a.split("").sort().join("");var c="' . $cipher_text . '";var d="";';
        $script .= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
        $script .= 'document.getElementById("' . $id . '").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';
        $script = "eval(\"" . str_replace(array("\\", '"'), array("\\\\", '\"'), $script) . "\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/' . $script . '/*]]>*/</script>';
        return '<span id="' . $id . '">[' . t('javascript protected email address') . ']</span>' . $script;

    }

    public static function maxStyleWidth($style, $maxWidth)
    {
        if (strpos($style, ';') !== false) {
            $styleModified = array();
            $styleList = explode(';', $style);
            $rawStylePairs = array();
            $width = 0;
            $height = 0;
            foreach ($styleList as $stylePair) {
                if (strpos($stylePair, ':')) {
                    list($key, $value) = explode(':', $stylePair);
                    $key = trim($key);
                    $value = trim($value);
                    $styleModified[$key] = $value;
                    if (strpos($value, 'px') && is_numeric(substr($value, 0, -2))) {
                        $number = substr($value, 0, -2);
                        if ($key == 'height') {
                            $height = $number;
                        }
                        if ($key == 'width') {
                            $width = $number;
                        }
                    }
                }
                else {
                    $rawStylePairs[] = $stylePair;
                }
            }

            if (!$width || !$height || ($width <= $maxWidth)) {
                return $style;
            }
            $height = round($height * $maxWidth / $width);
            $styleModified['height'] = $height . 'px';
            $styleModified['width'] = $maxWidth . 'px';

            $style = implode(';', $rawStylePairs);
            foreach ($styleModified as $key => $value) {
                $style .= ';' . implode(':', array($key, $value));
            }
            if (strpos($style, ';') === 0) {
                $style = substr($style, 1);
            }
            return $style;

        }
    }

    public static function getDetailsIcon($attributes, $dataModel, $url = 'javascript:void();', $options= array())
    {
        static $javaScript = false;
        $defaultOptions = array(
            'additionalStyle' => '',
            'wrapText' => false,
            'maxWidth' => '900px',
        );
        $options = CMap::mergeArray($defaultOptions,$options);
        $controller = app()->getController();
        $wrapStyle = '';
        if ($options['wrapText']){
            // it did not work, I think because its burried deep inside containers
            // tried to apply the style on individual links too but failed
            $defaultOptions['additionalStyle'] = $defaultOptions['additionalStyle'] . "
                -ms-word-break: break-all;
                word-break: break-all;
                word-break: break-word;
                -webkit-hyphens: auto;
                -moz-hyphens: auto;
                hyphens: auto;
                ";
        }
        $contents = $controller->widget('DetailView', array(
            'data' => $dataModel,
            'attributes' => $attributes,
            'htmlOptions' => array('style' => "table.detail-view
{
        background: white;
        border-collapse: collapse;
        margin: 0;
        {$options['additionalStyle']}

}",
            'class'=>'detail-icon'),
        ), true);
        if (!$javaScript){
            $javaScript = true;
            cs()->registerScript('detailWidth',"

setInterval(
function ()
{
            $('.detail-icon').parent().css('overflow','visible');
            $('.detail-icon').parent().parent().css('max-width','{$options['maxWidth']}');

}

,1300);
 ");}

//        $contents = "<div style='width:50px'>$contents</div>";

        $output = l(i(au() . '/icons/comments.png'), $url, array('title' => $contents));
        return $output;
    }
}