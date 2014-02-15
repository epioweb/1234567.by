<?
function PrintObject($obj, $hidden = false)
{
    $hiddenStyle = "";

    if($hidden)
         $hiddenStyle = "display:none;";

    echo "<pre style=\"background-color:#FFF;color:#000;font-size:10px;".$hiddenStyle."\">";
    print_r($obj);
    echo "</pre>";
}

function PrintAdmin($obj)
{
    global  $USER;
    if($USER->IsAdmin())
    {
      echo "<pre style=\"background-color:#FFF;color:#000;font-size:10px;\"><span style=\"color: red;\">PrintAdmin:</span>\n";
      print_r($obj);
      echo "</pre>";
    }
}