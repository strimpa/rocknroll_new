<?php

require_once '../globals.php';

if ((($_FILES["uploadedfile"]["type"] == "image/gif")
|| ($_FILES["uploadedfile"]["type"] == "image/jpeg")
|| ($_FILES["uploadedfile"]["type"] == "image/pjpeg"))
&& ($_FILES["uploadedfile"]["size"] < 100000))
  {
  if ($_FILES["uploadedfile"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["uploadedfile"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["uploadedfile"]["name"] . "<br />";
    echo "Type: " . $_FILES["uploadedfile"]["type"] . "<br />";
    echo "Size: " . ($_FILES["uploadedfile"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["uploadedfile"]["tmp_name"] . "<br />";

    if (file_exists($serverRoot."images/" . $_FILES["uploadedfile"]["name"]))
      {
      echo $_FILES["uploadedfile"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["uploadedfile"]["tmp_name"],
      "../../images/" . $_FILES["uploadedfile"]["name"]);
      echo "Stored in: " . $serverRoot."images/" . $_FILES["uploadedfile"]["name"];
      }
    }
  }
else
  {
  echo "Invalid file: type:".$_FILES["uploadedfile"]["type"].", size:".$_FILES["uploadedfile"]["size"]." bytes.";
  }
?>