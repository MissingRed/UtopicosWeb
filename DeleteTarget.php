<?php
header('Access-Control-Allow-Origin: *');
header(
  "Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"
);
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

new DeleteTarget($_GET["targetId"], $_GET["targetName"]);

class DeleteTarget
{
  private $access_key = "872193617f52547044d05e2ccf372c29cc65ed1b";
  private $secret_key = "4d5f0c38a2322404f163f9ab6dc9b1b82525986d";

  private $url = "https://vws.vuforia.com";
  private $requestPath = "/targets/";
  private $request;

  function __construct($targetId, $targetName)
  {
    $this->requestPath = $this->requestPath . $targetId;
    $this->execDeleteTarget($targetName);
  }

  private function execDeleteTarget($targetName)
  {
    $this->request = new HTTP_Request2();
    $this->request->setMethod(HTTP_Request2::METHOD_DELETE);

    $this->request->setConfig([
      'ssl_verify_peer' => false,
    ]);

    $this->request->setURL($this->url . $this->requestPath);

    $this->setHeaders();

    try {
      $response = $this->request->send();

      if (200 == $response->getStatus()) {
        if (file_exists($targetName . ".jpg")) {
          unlink($targetName . ".jpg");
        }

        if (file_exists($targetName . ".mp4")) {
          unlink($targetName . ".mp4");
        }
      }

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
