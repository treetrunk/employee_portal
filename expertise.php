<?php 

  $expertise = expertiseList();

  echo "
    <h2>Areas of Expertise</h2>
    <div style='width: 700px; margin-right: auto; margin-left: auto;'>
  ";

  foreach ($expertise as $cat) {

    $identifier = preg_replace("/[^a-zA-Z0-9]/", "", $cat);

    echo "
      <div class='togglebtn' onClick='hidediv(\"$identifier\")' id='$identifier-btn'><span id='$identifier-arw' style='float:right;font-weight:normal;'>&#9650; &nbsp;&nbsp;</span>&nbsp;&nbsp;$cat</div>
      <div class='togglediv' id='$identifier'>
        <ul>
    ";

    $sql = "SELECT * FROM staff WHERE description LIKE '%$cat%' ORDER BY lname";
    
    if ($result = mysql_query($sql)) {

      while ($staff = mysql_fetch_array($result)) {
        echo "
            <li><a href='?page=profile&profileid=$staff[id]'>$staff[fname] $staff[lname]</a> ($staff[location])</li>
        ";
      }

    } else {
      echo "None found.";
    }

    echo "
        </ul>
      </div>
    ";

  }


?>
