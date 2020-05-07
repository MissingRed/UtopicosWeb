<?php
require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';

class Get
{
  private $access_key = "872193617f52547044d05e2ccf372c29cc65ed1b";
  private $secret_key = "4d5f0c38a2322404f163f9ab6dc9b1b82525986d";

  private $url = "https://vws.vuforia.com";
  private $requestPath = "/targets/";
  private $request;
  private $datos;

  function __construct($targetId)
  {
    $this->requestPath = $this->requestPath . $targetId;
    $this->execGetTarget();
  }

  function getDato()
  {
    return $this->datos;
  }

  private function execGetTarget()
  {
    $this->request = new HTTP_Request2();
    $this->request->setMethod(HTTP_Request2::METHOD_GET);

    $this->request->setConfig([
      'ssl_verify_peer' => false,
    ]);

    $this->request->setURL($this->url . $this->requestPath);

    $this->setHeaders();

    try {
      $response = $this->request->send();

      $this->datos = $response->getBody();
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