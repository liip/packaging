<?php

define('PACKAGING_CONFIG_PATH', dirname(__FILE__) . '/../packaging_config.php');

function get_config($element = null) {
    if(!file_exists(PACKAGING_CONFIG_PATH)) {
        die("Error: the file packaging_config.php file doesn't exist, you should copy the packaging/packaging_config.php.sample and customize it first.\n");
    }

    $config = file_get_contents(PACKAGING_CONFIG_PATH);

    if($config === false) {
        die("Error: unable to read the contents of packaging_config.php.\n");
    }

    // Execute the configuration
    eval($config);

    $config = array(
        'configure' => $configure,
        'filemapping' => $filemapping,
    );

    if($element) {
        if(array_key_exists($element, $config)) {
            return $config[$element];
        }

        return null;
    }

    return $config;
}

function get_makefile_in() {
    $makefile_path = dirname(__FILE__) . '/Makefile.in';

    if(!file_exists($makefile_path)) {
        die("Error: the file packaging/Makefile.in doesn't exist.\n");
    }

    $makefile = file_get_contents($makefile_path);

    if($makefile === false) {
        die("Error: unable to read the contents of packaging/Makefile.in.\n");
    }

    return $makefile;
}

function get_install_cmd() {
    $dirs = array();
    $cmd = '';
    $filemapping = get_config('filemapping');

    foreach ($filemapping as $dest => $files) {
        if(!is_array($files)) {
            $files = array($files);
            $dirs[dirname($dest)] = dirname($dest);
        }
        else {
            $dirs[$dest] = $dest;
        }

        foreach($files as $src) {
            $cmd .= "\tcp -a \$(PTMP)/src/$src \$(PTMP)/build/$dest\n";
        }
    }

    $dircmd = '';
    foreach($dirs as $dir) {
        $dircmd .= "\tmkdir -p \$(PTMP)/build/$dir\n";
    }

    return $dircmd . $cmd;
}

function template($str, $options, $flags = array()) {
    foreach($options as $option => $value) {
        if(array_key_exists($option, $flags)) {
            $concatenated_val = '';

            if(is_array($value)) {
                foreach($value as $flag) {
                    $concatenated_val .= $flags[$option] . ' ' . $flag . ' ';
                }
            }
            else {
                if($value) {
                    $concatenated_val = $flags[$option] . ' ' . $value;
                }
            }

            $str = replace($str, $option, $concatenated_val);
        }
        else {
            if(!is_array($value)) {
                $str = replace($str, $option, $value);
            }
        }
    }

    return $str;
}

function template_file($file, $options, $flags = array()) {
    if(!file_exists($file)) {
        return false;
    }

    $file_contents = file_get_contents($file);
    $templated_file = template($file_contents, $options, $flags);

    $fp = fopen($file, 'w');
    fputs($fp, $templated_file);
    fclose($fp);

    return true;
}

function replace($str, $option, $value) {
    $option = sprintf('@%s@', strtoupper($option));
    $value = trim($value);

    return str_replace($option, $value, $str);
}

