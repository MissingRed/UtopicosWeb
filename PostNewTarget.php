<?php
header('Access-Control-Allow-Origin: *');
header(
  "Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"
);
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';
require_once 'UploadTargets.php';

new UploadTargets($_FILES["fileToUpload"]["tmp_name"], $_POST["targetName"], 0);
new UploadTargets(
  $_FILES["metadata"]["tmp_name"],
  $_POST["targetName"],
  $_POST["type"]
);

new PostNewTarget($_POST["targetName"], $_POST["type"]);

class PostNewTarget
{
  private $access_key = "872193617f52547044d05e2ccf372c29cc65ed1b";
  private $secret_key = "4d5f0c38a2322404f163f9ab6dc9b1b82525986d";

  private $url = "https://vws.vuforia.com";
  private $requestPath = "/targets";
  private $request;
  private $jsonRequestObject;

  function __construct($targetName, $type)
  {
    $imageLocation = $targetName . ".jpg";

    if ($type == 1) {
      $metadataLocation = $targetName . ".mp4";
    } elseif ($type == 2) {
      $metadataLocation = $targetName . ".jpg";
    }

    $this->jsonRequestObject = json_encode([
      'width' => 320.0,
      'name' => $targetName,
      'image' => $this->getImageAsBase64($imageLocation),
      'application_metadata' => base64_encode(
        "http://danielrf.com/api_vuforia/" . $metadataLocation
      ),
      'active_flag' => 1,
    ]);

    $this->execPostNewTarget($imageLocation);
  }

  function getMetadataAsBase64($metadataLocation)
  {
    $file = file_get_contents($metadataLocation);

    if ($file) {
      $file = base64_encode($file);
    }

    return $file;
  }

  function getImageAsBase64($imageLocation)
  {
    $file = file_get_contents($imageLocation);

    if ($file) {
      $file = base64_encode($file);
    }

    return $file;
  }

  public function execPostNewTarget()
  {
    $this->request = new HTTP_Request2();
    $this->request->setMethod(HTTP_Request2::METHOD_POST);
    $this->request->setBody($this->jsonRequestObject);

    $this->request->setConfig([
      'ssl_verify_peer' => false,
    ]);

    $this->request->setURL($this->url . $this->requestPath);

    $this->setHeaders();

    try {
      $response = $this->request->send();

      echo $response->getBody();
    } catch (HTTP_Request2_Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  private function setHeaders()
  {
    $sb = new SignatureBuilder();
    $date = new DateTime("now", new DateTimeZone("GMT"));

    $this->request->setHeader('Date', $date->format("D, d M Y H:i:s") . " GMT");
    $this->request->setHeader("Content-Type", "application/json");
    $this->request->setHeader(
      "Authorization",
      "VWS " .
        $this->access_key .
        ":" .
        $sb->tmsSignature($this->request, $this->secret_key)
    );
  }
}
?>
