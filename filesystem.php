<?php 
// print_r(scandir("data"));

function getFileExtensionIcon($extension){
    $extension = strtolower($extension);
    switch($extension)
    {
        case 'folder': return '<i class="fa-regular fa-folder"></i>';
        case 'png': return '<i class="fa-regular fa-image"></i>';
        case 'js': return '<i class="fa-brands fa-js"></i>';
        case 'txt': return '<i class="fa-solid fa-file-lines"></i>';
        case 'pptx': return '<i class="fa-regular fa-file-powerpoint"></i>';
        case 'md': return '<i class="fa-brands fa-mdb"></i>';
        case 'git': return '<i class="fa-brands fa-git"></i>';
        case 'gitignore': return '<i class="fa-brands fa-git"></i>';
        case 'json': return '<i class="fa-brands fa-node-js"></i>';
        case 'm4a': return '<i class="fa-brands fa-itunes-note"></i>';
        case 'jpg': return '<i class="fa-regular fa-image"></i>';
        case 'gif': return '<i class="fa-regular fa-image"></i>';
        case 'mp4': return '<i class="fa-solid fa-video"></i>';
        case 'env': return '<i class="fa-solid fa-tree"></i>';
        default: return '<i class="fa-solid fa-question"></i>';
    }
}
function getIcon($icon)
{
    switch($icon)
    {
        case 'rename': return '<i class="fa-solid fa-pencil"></i>';
        case 'delete': return '<i class="fa-solid fa-trash"></i>';
        case 'copy': return '<i class="fa-solid fa-copy"></i>';
        case 'direct-link': return '<i class="fa-solid fa-link"></i>';
        case 'download': return '<i class="fa-solid fa-download"></i>';
        default: return '<i class="fa-solid fa-question"></i>';
    }
}
function getPathBack($fullPath)
{
    $base = "/fileview.php";
    $arrOfFolders = explode("/", str_replace("//",'/',$fullPath));
    if(count($arrOfFolders) <= 2) return $base;
    array_pop($arrOfFolders);
    array_shift($arrOfFolders);
    $newPath = "$base?path=";
    foreach($arrOfFolders as $folder)
        $newPath .= "$folder/";

    $pathWithoutLastSlash = substr($newPath, 0, -1);

    return $pathWithoutLastSlash === ''? $base : $pathWithoutLastSlash;
}

function getShortenedFileName($maxSize, $name)
{
    if(strlen($name) > $maxSize)
        return substr($name, 0, $maxSize)."...";
    return $name;
}

function getOptimizedFileSize($fileSize){
    $newFileSize=$fileSize;
    $symbols = array("B", "KB", "MB", "GB", "TB");
    $symbolIndex = 0;
    while ($newFileSize >= 1024 && $symbolIndex < count($symbols) - 1) {
        $newFileSize /= 1024;
        $symbolIndex++;
    }
    $newFileSize = round($newFileSize, 2);
    return "$newFileSize".$symbols[$symbolIndex];
}

function getOptimizedPath($fullPath)
{
    $pathWithoutRootFolder = str_replace("data/","",$fullPath);
    $strExist = strlen($pathWithoutRootFolder)>0;
    if($strExist && substr($pathWithoutRootFolder,-1)==='.') $pathWithoutRootFolder=substr($pathWithoutRootFolder, 0, -2);
    if($strExist && $pathWithoutRootFolder[0]==='/')
        $pathWithoutRootFolder = substr($pathWithoutRootFolder, 1);
    
    return $pathWithoutRootFolder === '' ? 'fileview.php' : "?path=$pathWithoutRootFolder";
}

function getExtension($fullPath)
{
    if(isset(pathinfo($fullPath)['extension']))
        return pathinfo($fullPath)['extension'];
    return "folder";
}

function sortFilesComparison($a, $b) {
    if (is_dir("data/$a") && !is_dir("data/$b")) {
        return -1;
    } elseif (!is_dir("data/$a") && is_dir("data/$b")) {
        return 1;
    } else {
        return strcmp($a, $b);
    }
}
function checkIfPathIsInBaseDirectory($path){
    $baseDirectory = realpath('C:/xampp/htdocs/data');
    $fullPath = realpath($baseDirectory . '/' . $path);
    if (strpos($fullPath, $baseDirectory) !== 0) {
        header('HTTP/1.0 403 Forbidden');
        exit("Access denied");
    }
}

function display($path)
{
    checkIfPathIsInBaseDirectory($path);
    $fullPath = "data/$path";
    if(is_dir($fullPath)){
        $scannedDir = scandir($fullPath);
        $pathBack = getPathBack($fullPath);
        $maxFileNameSize = 20;

        usort($scannedDir, "sortFilesComparison");
        foreach($scannedDir as $id => $file)
        {
            
            $pathToFile = $fullPath."/$file";
            if(($path==="") && ($file===".."|| $file==="."))
                continue;
            $fileName = getShortenedFileName($maxFileNameSize, $file);
            $extension = getExtension(($pathToFile));
            $fileSize = getOptimizedFileSize(filesize($pathToFile));
            $icon = getFileExtensionIcon($extension);
            $optimizedPath = $file===".." ? $pathBack : getOptimizedPath($pathToFile);
            $lastModifiedTimestamp = date("Y-m-d H:i",filemtime($pathToFile));

            echo "<tr class='even:bg-blue-50 hover:bg-slate-200' data-key='$id'>
                <td class='px-4'>
                    <input type='checkbox' class='checkbox-$id'/>
                </td>
                <td><a class='text-blue-500 hover:underline" . (is_dir($pathToFile) ? "" : " file") . "' href='$optimizedPath'>$icon<span key='$id'> $fileName</span></a></td>
                <td>$extension</td>
                <td>$fileSize</td>
                <td>$lastModifiedTimestamp</td>
                <td class='actions flex gap-2'>
                    <button class='text-red-400 hover:text-red-500' onclick='deleteItem($id, `$pathToFile`)'>". getIcon("delete") ."</button>
                    <button class='text-cyan-400 hover:text-cyan-500' onclick='renameItem(`$file`, `$pathToFile`, `$id`)'>". getIcon("rename") ."</button>
                    <button class='text-violet-400 hover:text-violet-500' onclick='copyItem(`$pathToFile`, `$file`)'>". getIcon("copy") ."</button>"
                    .
                    (is_dir($pathToFile) ? "" : "<a class='text-orange-400 hover:text-orange-500' href='$pathToFile' download='$file'>". getIcon("download") ."</button>")
                    .
                "</td>
            </tr>";
        }
    }
}


?>
