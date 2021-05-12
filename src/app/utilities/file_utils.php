<?php
    namespace app\file_utils;

    include_once "./app/classes/file_list_types.php";

    function sanitise_file_name(string $dir, string $base){
        $s = str_replace($base, "", $dir);
        return rtrim($s, ".");
    }

    function file_list_to_array(string $dir, int $file_list_type, string $extension, bool $ascending){
        $a = [];

        if (is_dir($dir)){
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));

            foreach ($iterator as $key => $file) {
                if ((is_dir($key) && $file_list_type == \app\file_list_types\file_list_type::dirs_only)
                   || (!is_dir($key) && $file_list_type == \app\file_list_types\file_list_type::files_only) && pathinfo($file, PATHINFO_EXTENSION) == $extension){
                    $a[] = sanitise_file_name($file, $dir);
                }
            }

            $a = array_unique($a);

            if($ascending){
                sort($a);
            }else{
                rsort($a);
            }
        }

        return $a;
    }

    function file_from_query_string(string $base, int $file_list_type){
        $s = '';
        $query_strings = '';

        $flt = new \app\file_list_types\file_list_type($file_list_type);
        $key = $flt->to_string();

        if(!empty($key)){
            $query_string = filter_input(INPUT_SERVER, "QUERY_STRING", FILTER_SANITIZE_URL);
            parse_str($query_string, $query_strings);

            if (array_key_exists($key, $query_strings) && !empty($query_strings[$key])){
                $s = $query_strings[$key];
            }
        }

        return $s;
    }

    function array_to_dropdown(array $array, string $base, string $url){
        echo '<div class="dropdown">'.PHP_EOL;
        echo '<a class="btn btn-secondary dropdown-toggle swat-btn-dropdown" href="#" role="button" id="dropdown-menu-link" data-bs-toggle="dropdown" aria-expanded="false">'.PHP_EOL;
        echo 'Select directory'.PHP_EOL;
        echo '</a>'.PHP_EOL;
        echo '<ul class="dropdown-menu" aria-labelledby="dropdown-menu-link">'.PHP_EOL;
            foreach($array as $item) {
                echo '<li><a class="dropdown-item" href="'.$url.'?d='.$item.'&f='.file_from_query_string($base, \app\file_list_types\file_list_type::files_only).'">'.$item.'</a></li>'.PHP_EOL;
            }
        echo '</ul>'.PHP_EOL;
        echo '</div>'.PHP_EOL;
    }

    function array_to_list_group(array $array, string $base, string $url){
        echo '<div class="list-group swat-logs-div">'.PHP_EOL;

        $item_count = 0;

        foreach($array as $item) {
            $item_count++;
            $class = $item_count % 2 == 0 ? 'dark' : 'light';
            echo '<a href="'.$url.'?d='.file_from_query_string(__DIR__, \app\file_list_types\file_list_type::dirs_only).'&f='.$item.'" class="list-group-item list-group-item-action list-group-item-'.$class.'">'.$item.'</a>'.PHP_EOL;
        }

        if($item_count === 0){
            echo '<span class="list-group-item list-group-item-action list-group-item-light">Empty directory</span>';
        }

        echo '</div>'.PHP_EOL;
    }

    function display_log_file(string $base, string $dir, string $file){
        echo '<div class="h-100">'.PHP_EOL;
        echo '<div class="card h-100" >'.PHP_EOL;
        echo '<code contenteditable="true" class="form-control card-body swat-logs-div id="swat-ini-file" style="font-family: Consolas,monaco,monospace; font-size: 12px;">'.PHP_EOL;

        $full_file = $base.$dir.$file;
        $_file = file_exists($full_file) && !empty($file) ? $file : '-';
        $_dir = file_exists($full_file) && !empty($dir) ? $dir : '-';

        if(file_exists($full_file) && !empty($file)){
            $contents = file_get_contents($full_file);
            $lines = explode("\n", $contents);

            foreach($lines as $line) {
                if (substr($file, 0, 3) == "api"){
                    echo $line.'<br>'.PHP_EOL;
                }else{
                    echo format_log_text($line);
                }
            }
        }

        echo '</code>'.PHP_EOL;
        echo '<ul class="list-group list-group-flush">'.PHP_EOL;
        echo '<li class="list-group-item"><b>File:</b> '.$_file.'</li>'.PHP_EOL;
        echo '<li class="list-group-item"><b>Directory:</b> '.$_dir.'</li>'.PHP_EOL;
        echo '</ul>'.PHP_EOL;
        echo '</div>'.PHP_EOL;
        echo '</div>'.PHP_EOL;
    }

    function format_ini_text(string $line){
        if (strlen($line) > 0){
            switch ($line[0]) {
                case ";":
                    $colour = "green";
                    break;
                case "[":
                    $colour = "blue";
                    break;
                default:
                    $colour = "default";
                    break;
            }

            return '<span style="color: '.$colour.'">'.$line.'</span><br>'.PHP_EOL;
        }
    }

    function format_log_text(string $line){
        $a = explode('][', $line);
        $s = '';
        $i = 0;

        foreach($a as $section) {
            if(!empty($section)){
                switch ($i) {
                    case 0:
                        $s .= '<span style="color: purple">'.$section.']</span>';
                        break;
                    case 1:
                        switch ($section) {
                            case 'DEB':
                                $colour = 'green';
                                break;
                            case 'INF':
                                $colour = 'yellow';
                                break;
                            case 'ERR':
                                $colour = 'orange';
                                break;
                            case 'FAT':
                                $colour = 'red';
                                break;
                            default:
                                $colour = 'default';
                                break;
                        }

                        $s .= '<span style="color: '.$colour.'">['.$section.']</span>';
                        break;
                    case 2:
                        $s .= '<span style="color: blue">['.$section.'</span>';
                        break;
                    default:
                        $s .= '<span style="color: default">'.$section.'</span>';
                        break;
                }

                $i++;
            }
        }


        return $s.'<br>'.PHP_EOL;
    }

    function display_ini_file(string $base, string $dir, string $file){
        /*echo '<form class="h-100"  name="input" action="" method="post">'.PHP_EOL;*/
        echo '<div class="card h-100" >'.PHP_EOL;
        echo '<code contenteditable="true" class="form-control card-body swat-logs-div" id="swat-ini-file" style="font-family: Consolas,monaco,monospace; font-size: 12px;" name="swat-config-contents">'.PHP_EOL;

        $full_file = $base.$dir.$file;
        $_file = file_exists($full_file) && !empty($file) ? $file : '-';
        $btn_state = file_exists($full_file) && !empty($file) ? '' : 'disabled';

        if(file_exists($full_file) && !empty($file)){
            $contents = file_get_contents($full_file);
            $lines = explode("\n", $contents);

            foreach($lines as $line) {
                echo format_ini_text($line);
            }
        }

        echo '</code>'.PHP_EOL;
        echo '<ul class="list-group list-group-flush">'.PHP_EOL;
        echo '<li class="list-group-item"><b>File:</b> '.$_file.'</li>'.PHP_EOL;
        //echo '<li class="list-group-item"><button type="submit" class="btn btn-swat btn-sm btn-swat-save-fr '.$btn_state.'" name="swat-config-save-contents">Save</button></li>'.PHP_EOL;
        echo '</ul>'.PHP_EOL;
        echo '</div>'.PHP_EOL;
        /*echo '</form>'.PHP_EOL;*/
    }
