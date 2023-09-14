<?php 
include 'filesystem.php';
$path='';
if(isset($_GET['path']))
    $path = $_GET['path'];
$root='<a href="/fileview.php" class="hover:underline">root</a>/';
// $rename = $_POST()
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File system</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/6b94fa1050.js" crossorigin="anonymous"></script>
    <style>
        th{
            text-align: left;
        }
       td{
        padding: .25rem 0;
       }
       a, button{
        cursor: pointer;
       }
       
    </style>
</head>
<body>
    <!-- <div id="modal" class="fixed top-0 left-0 bg-opacity-50 bg-black h-[100vh] w-[100vw] flex justify-center items-center">
       <div class="bg-white rounded p-4 min-w-[400px] min-h-[400px] max-w-[80%] max-h-[80%] overflow-hidden">
            <div class="hover:bg-slate-200 cursor-pointer rounded-xl py-1 px-2 relative left-[90%] w-min" onclick="toggleModal()">
                <i class="fa-solid fa-x fa-xl text-red-500 hover:text-red-600 "></i>
            </div>
            
        </div>
    </div> -->
    <div class="mb-7 bg-teal-700 py-4">
        <div class="container mx-auto text-white flex justify-between">
            <div><?= $path === '' ? $root : str_replace("//","/", "$root$path") ?></div>
            <div class="flex justify-between gap-4">
                <span class="hover:underline cursor-pointer">Upload</span>
                <span class="hover:underline cursor-pointer" onclick="pasteItem('data/<?= $path ?>')">Paste</span>
                <span class="hover:underline cursor-pointer">Move</span>
            </div>
        </div>
    </div>

    
    <table class="border-black border-[1px] border-collapse w-full xl:text-lg">
        <thead>
            <tr class="odd:bg-blue-50  border-black border-[1px]">
                <th class="px-4"><input type="checkbox" class="select-all"></th>
                <th>File name</th>
                <th>Extension</th>
                <th>Size</th>
                <th>Modified</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php display($path) ?>
        </tbody>
    </table>
    <script>
        const deleteItem = (id, path)=>{
            const confirmation = confirm("Do you really want to remove this file?");
            if(!confirmation)
            return;
            const row = document.querySelector(`[data-key="${id}"]`).remove();
            const data = new FormData();
            data.append("removePath", path);
            fetch("query.php", {method: "POST", body: data})
            .then(response=>response.text())
            .then(result=>console.log(result))
            .catch(err=>console.log(err));
        }
        const renameItem = (oldName, path, id)=>{
            const fileName = prompt("Enter a new name for the file:");
            if(!fileName)
                return;
            const data = new FormData();
            data.append("path", path);
            data.append("oldName", oldName);
            data.append("newName", fileName);
            fetch("query.php", {
            method: "POST",
            body: data,
            })
            .then((response) => response.text())
            .then((result) => {
                document.querySelector(`[key="${id}"]`).innerText=" "+fileName;
            })
            .catch((error) => {
                console.error("Error:", error);
            }); 
        }


        const copyItem = (path, file)=>{
            localStorage.setItem("path", path);
            localStorage.setItem("file", file);
        }

        const pasteItem = (path) => {
            const copyFrom = localStorage.getItem("path");
            const file = localStorage.getItem("file");
            // console.log(copyFrom);
            const data = new FormData();
            data.append("copyFrom", copyFrom);
            data.append("file", file);
            data.append("pasteTo", path);
            fetch("query.php", {method:"POST", body: data})
            .then((resp)=> resp.text())
            .then((data)=>{
                window.location.reload(true);
            })
            .catch((err)=>console.log(err));
        }
        const toggleModal = () => {
            document.querySelector("#modal").classList.toggle("hidden");
        }
    </script>

</body>
</html>