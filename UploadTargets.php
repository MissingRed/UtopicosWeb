<?php
header('Access-Control-Allow-Origin: *');
header(
  "Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method"
);
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class UploadTargets
{
  function __construct($img, $targetName, $tipo)
  {
    if ($tipo != "1") {
      $targetName = $targetName . ".jpg";

      if (
        $this->isFake($img) &&
        $this->exist(
          $targetName
        ) /*&& $this->checkSize($img) && $this->checkType($img)*/
      ) {
        file_put_contents($targetName, file_get_contents($img));
      }
    } else {
      $targetName = $targetName . ".mp4";
      file_put_contents($targetName, file_get_contents($img));
    }
  }

  function isFake($img)
  {
    if (getimagesize($img)) {
      return true;
    } else {
      return false;
    }
  }

  function exist($targetName)
  {
    if (file_exists($targetName)) {
      return false;
    } else {
      return true;
    }
  }

  function checkSize($img)
  {
    if ($img > 500000) {
      return false;
    } else {
      return true;
    }
  }

  function checkType($img)
  {
    if (basename($img) != "jpg") {
      return false;
    } else {
      return true;
    }
  }
}
?>
