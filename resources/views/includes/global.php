<?php
    
    function my_laravelEncode($param)
    {
        if($param == null || $param == "" ) return "";
        $param = str_replace('/', '~', $param);
        return $param;
    }
?>