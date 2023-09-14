<?php

if($_SERVER["REQUEST_METHOD"]==="POST")
{
    if(isset($_POST["newName"]))
    {
        $path = $_POST["path"];
        $newName = $_POST["newName"];
        $oldName = $_POST["oldName"];
        $path = str_replace($oldName, "",$path);
        rename("$path$oldName", "$path$newName");
        echo $path;
    }
    if(isset($_POST["removePath"]))
    {
        $remove = $_POST["removePath"];
        unlink($remove);
    }
    if(isset($_POST["pasteTo"]))
    {
        $pasteTo = $_POST["pasteTo"];
        $copiedPath = $_POST["copyFrom"];
        $file = $_POST["file"];
        copy($copiedPath, "$pasteTo/$file");
        echo "file pasted successfully!";
    }
}

?>
