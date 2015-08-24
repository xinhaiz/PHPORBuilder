<?php

namespace Lib;

final class Func {
    
    public static function uc($name) {
        $options = \Lib\Options::getInstance();
        $ucwords = $options->getUcwords();
         
        $name = $ucwords === true ? str_replace(' ', '_', (ucwords(str_replace('_', ' ', $name)))) : ucfirst($name);

        if($options->getUnderline() === false) {
            $name = str_replace('_', '', $name);
        }
        
        return $name;
    }
    
}