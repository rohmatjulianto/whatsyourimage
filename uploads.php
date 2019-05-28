<?php
    require_once 'vendor/autoload.php';
    require_once "./random_string.php";
    
    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;    

      # Setup a specific instance of an Azure::Storage::Client
      $connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
    
      // Create blob client.
      $blobClient = BlobRestProxy::createBlobService($connectionString);
      
      # Create the BlobService that represents the Blob service for the storage account
      $createContainerOptions = new CreateContainerOptions();
      
      $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
      
      // Set container metadata.
      $createContainerOptions->addMetaData("key1", "value1");
      $createContainerOptions->addMetaData("key2", "value2");
  
      $containerName = "blockblobs-joule".generateRandomString();
  
      try{
          // Create container.
          $blobClient->createContainer($containerName, $createContainerOptions);

        } catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        } catch(InvalidArgumentTypeException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
    }

// $uploaddir = '';
// $fileToUpload = $uploaddir . basename($_FILES['images']['name']);
$temp = explode(".", $_FILES["images"]["name"]);
$fileToUpload =  $containerName.'.' . end($temp);
echo '<pre>';
if (move_uploaded_file($_FILES['images']['tmp_name'], $fileToUpload)) {
    
    echo "File is valid, and was successfully uploaded.\n";

    $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
    fclose($myfile);

    # Upload file as a block blob
    echo "Uploading BlockBlob: ".PHP_EOL;
    echo $fileToUpload;
    echo "<br />";
    
    $content = fopen($fileToUpload, "r");

    //Upload blob
    $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
    header('location: index.php?containerName='.$containerName);

} else {
    header('location: index.php');

    // echo "Possible file upload attack!\n";
}

// echo 'Here is some more debugging info:';
// print_r($_FILES);

// print "</pre>";

?>