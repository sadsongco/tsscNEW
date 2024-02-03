<?php

$dir_path = "../press_resources/";
$output = [];

if (is_dir($dir_path))
{
    if ($dh = opendir($dir_path))
    {
        $output[] = recursive_dir($dir_path, $dh);
        closedir($dh);
    }
}

echo json_encode($output);

function recursive_dir($dir, $dh) {
    $this_dir_arr = [];
    while ((($file = readdir($dh)) != false))
    {
        if ($file[0] != ".")
        {
            if (is_dir($dir.$file))
            {
                if ($file != "fullres" && $file != "big")
                {
                    $new_dh = opendir($dir.$file."/");
                    $this_dir_arr[$dir.$file."/"] = recursive_dir($dir.$file."/", $new_dh);
                    closedir($new_dh);
                }
            }
            else
            {
                $this_dir_arr[] = $file;
            }
        }
    }
    return $this_dir_arr;
}

?>