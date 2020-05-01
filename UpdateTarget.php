<?php
require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

new UpdateTarget($_GET["targetId"], $_GET["metadata"]);

class UpdateTarget
{
  private $access_key = "79555486c9a2a4a421f47fe4dcada9ede670c9bc";
  private $secret_key = "b84d1921cbf5c3de85434ad6a1c13531ee107b9b";

  private $url = "https://vws.vuforia.com";
  private $requestPath = "/targets/";
  private $request;
  private $jsonBody = "";

  function __construct($targetId, $metadata)
  {
    $this->requestPath = $this->requestPath . $this->targetId;

    $this->jsonBody = json_encode([
      'application_metadata' => base64_encode(
        "https://d28xe8vt774jo5.cloudfront.net/champion-abilities/0064/ability_0064_R1.webm"
      ),
    ]);

    $this->execUpdateTarget();
  }

  public function execUpdateTarget()
  {
    $this->request = new HTTP_Request2();
    $this->request->setMethod(HTTP_Request2::METHOD_PUT);
    $this->request->setBody($this->jsonBody);

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
