<?php
require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

new DeleteTarget($_GET["targetId"], $_GET["targetName"]);

class DeleteTarget
{
  private $access_key = "79555486c9a2a4a421f47fe4dcada9ede670c9bc";
  private $secret_key = "b84d1921cbf5c3de85434ad6a1c13531ee107b9b";

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

      if (200 == $response->getStatus() && file_exists($targetName . ".jpg")) {
        unlink($targetName . ".jpg");
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
