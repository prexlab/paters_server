<?php

namespace App\Services;

class Form {

    static function select($name, $arr, $val=null, $opt=null){

        if(is_array($opt)){
            foreach($opt as $k=>$v){
                $ret[] = sprintf('%s="%s"', $k, $v);
            }
            $opt = implode(' ', $ret);
        }

        $str = "<select name='$name' id='{$name}' {$opt}>"; 
    
        if(is_numeric($val)) $val = strval($val);
        foreach($arr as $k=>$v){
            if(is_numeric($k)) $k = strval($k);
            $sel = ($k === $val)?('selected'):('');
            $str .= "<option value='$k' $sel>$v</option>"; 
        }
        $str .= "</select>"; 
        return $str;
    }
    

    static function radio($name, $arr, $val=null){
    
        if(is_numeric($val)) $val = strval($val);
        
        $gl = strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')?"<span class='nwrp'>-</span>":""; 
        
        $str = '';
        foreach($arr as $k=>$v){
            if(is_numeric($k)) $k = strval($k);
            
            $ckd = ($k === $val)?('checked'):('');
            $str .= "<label for='form_{$name}_{$k}' style='white-space:nowrap;'>{$gl}<input type='radio' name='{$name}' id='form_{$name}_{$k}' value='$k' $ckd>{$v} </label> "; 
        }
        return $str;
    }

    static function checkbox($name, $arr, $val=array()){
    
        $val = (is_array($val))?($val):(explode('<>',$val));
        $val = array_map('strval', $val);
        
        $gl = strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')?"<span class='nwrp'>-</span>":""; 
    
        $str = '';
        foreach($arr as $k=>$v){
            if(is_numeric($k)) $k = strval($k);
            $ckd = (in_array($k, $val, 1))?('checked'):('');
            $str .= "<label for='form_{$name}_{$k}' style='white-space:nowrap'>
            {$gl}<input type='checkbox' name='{$name}[]' id='form_{$name}_{$k}' value='$k' $ckd>{$v}</label> "; 
    
        }
        return $str;
    }

    static function dsp_checkbox($dat, $arr, $dl, $gl){
        
        $str = array();
        
        $dat = (is_array($dat))?($dat):(explode($dl, $dat));
        foreach($dat as $v){
            if(!empty($arr[$v])) $str[] = $arr[$v];
        }
        return join($gl, $str);
    }

}
