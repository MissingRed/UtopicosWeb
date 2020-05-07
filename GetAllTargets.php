<?php
header('Access-Control-Allow-Origin: *');
header(
  "Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"
);
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

require_once 'HTTP/Request2.php';
require_once 'SignatureBuilder.php';
require_once 'Get.php';

new GetAllTargets();

class GetAllTargets
{
  private $access_key = "872193617f52547044d05e2ccf372c29cc65ed1b";
  private $secret_key = "4d5f0c38a2322404f163f9ab6dc9b1b82525986d";

  private $url = "https://vws.vuforia.com";
  private $requestPath = "/targets";
  private $request;

  function __construct()
  {
    $this->requestPath = $this->requestPath;
    $this->execGetAllTargets();
  }

  private function execGetAllTargets()
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

      $aux = json_decode($response->getBody());

      $cant = count($aux->results);
      $cont = 0;

      echo "[";

      foreach ($aux->results as $result) {
        $instancia = new Get($result);

        echo $instancia->getDato();

        if ($cont < $cant - 1) {
          echo ",";
          $cont++;
        }
      }

      echo "]";
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
