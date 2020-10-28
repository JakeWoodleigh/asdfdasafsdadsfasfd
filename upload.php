<?php

// https://www.php.net/manual/en/features.file-upload.php#114004 


header('Content-Type: text/plain; charset=utf-8');



try {

    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (!isset($_FILES['fileToUpload']['error']) || is_array($_FILES['fileToUpload']['error'])){
		var_dump($_FILES);
        throw new RuntimeException('Invalid parameters.');
    }

    // Check $_FILES['fileToUpload']['error'] value.
	
    switch ($_FILES['fileToUpload']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
    }


    // DO NOT TRUST $_FILES['fileToUpload']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['fileToUpload']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

    // You should name it uniquely.
    // DO NOT USE $_FILES['fileToUpload']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    if (!move_uploaded_file(
        $_FILES['fileToUpload']['tmp_name'],
        sprintf('./uploads/%s.%s',
            sha1_file($_FILES['fileToUpload']['tmp_name']),
			$ext
        )
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    echo "File is uploaded successfully. \n";
	echo "Running Python Backend now! \n";
	sleep(3);
	//-------------------------------------------------------------------------------
	// Here we hand off full control to the python backend, which will check if a file called done.txt has been made, if so it
	// will continue its process, if not it'll check soon
	// This is due to the uploaded image not being written until we exit the php script, which means we cannot call python
	// from inside here
	$file = fopen("done.txt", "w"); // open with write perms
	header("Location: http://49.176.24.198:8080/proc.html");
	die();

	
} catch (RuntimeException $e) {

    echo $e->getMessage();

}

?>