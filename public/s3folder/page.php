<!doctype html>
<head>
</head>
<body>
<?php
//include the S3 class              
if (!class_exists('S3'))require_once('S3.php');
 
//AWS access info
if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIJWRXGDVIYS77XQA');
if (!defined('awsSecretKey')) define('awsSecretKey', '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');
 
//instantiate the class
$s3 = new S3(awsAccessKey, awsSecretKey);
 
//we'll continue our script from here in step 4!
//check whether a form was submitted
if(isset($_POST['Submit'])){
 
    //retreive post variables
    $fileName = $_FILES['theFile']['name'];
    $fileTempName = $_FILES['theFile']['tmp_name'];
     
    //we'll continue our script from here in the next step!
}

//move the file
if ($s3->putObjectFile($fileTempName, "burunvideo", $fileName, S3::ACL_PUBLIC_READ)) {
    echo "We successfully uploaded your file.";
}else{
    echo "Something went wrong while uploading your file... sorry.";
} 
?>
<h2>Upload a file</h2>
<p>Click the Browse button and press Upload to start uploading your file</p>
<form action="" method="post" enctype="multipart/form-data">
  <input name="theFile" type="file" />
  <input name="Submit" type="submit" value="Upload">
</form>
</body>
</html>
