<?php
include("../includes/config.php");
include("../includes/functions.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

require_once("../vendor/autoload.php");

use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;

$client = new ImageAnnotatorClient(
	'keyFile' => json_decode(file_get_contents('../radtriads-2020-ef911bed123c.json'), true)
);  

// Annotate an image, detecting faces.
$annotation = $client->annotateImage(
    fopen('/data/photos/family_photo.jpg', 'r'),
    [Type::FACE_DETECTION]
);

// Determine if the detected faces have headwear.
foreach ($annotation->getFaceAnnotations() as $faceAnnotation) {
	$likelehood = Likelihood::name($faceAnnotation->getHeadwearLikelihood());
    echo "Likelihood of headwear: $likelehood" . PHP_EOL;
}
?>