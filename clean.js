var fs = require('fs');

function deleteFolderRecursive(path) {
    if (fs.existsSync(path) && fs.lstatSync(path).isDirectory()) {
        fs.readdirSync(path).forEach(function (file, index) {
            var curPath = path + "/" + file;

            if (curPath.includes("uploads"))
                return;

            if (fs.lstatSync(curPath).isDirectory()) { // recurse
                deleteFolderRecursive(curPath);
            } else { // delete file
                fs.unlinkSync(curPath);
            }
        });

        console.log(`Deleting directory "${path}"...`);
        //fs.rmdirSync(path);
    }
};

console.log("Cleaning working tree...");

deleteFolderRecursive("D:\\wamp64\\www\\");

console.log("Successfully cleaned working tree!");