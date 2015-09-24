<?php


   /////////////////////
  //  FILL SIDEBARS  //
 /////////////////////

function fillSidebar($start, $end, $field) {

  $sidebar = array();

  $dateunits = array (
    'year' => 31536000,
    'month' => 2592000,
    'week' => 604800,
    'day' => 86400,
    'hour' => 3600,
    'minute' => 60,
    'second' => 1
  );


  $oldestdate = time() - $start;
  $newestdate = time() + $end;
  
  $sql = "SELECT * FROM staff";
  if ($field != 'leaveday') { $sql .= " WHERE leaveday IS NULL"; }
  $result = mysql_query($sql);

  if ($result) {

//////  CREATE EMPLOYEE ARRAY  //////

    while ($staff = mysql_fetch_array($result)) {

      if ($field == 'anniversary') { $day = strtotime($staff['startday']); } 
        else { $day = strtotime($staff["$field"]); }

      if ($field == 'birthday' || $field == 'anniversary') {
        $day = strtotime(date('M j ', $day) . date('Y', time()));
      }
      
      if ($day > $oldestdate && $day < $newestdate) {

        if ($field == 'anniversary') { 
          $amount = strval(($day - strtotime($staff['startday'])) / $dateunits['year']);
          $staffdate = "<br />($amount" . "yrs - " . date('n/j)', strtotime($staff['startday']));
        } else {
          $staffdate = date('(n/j)', strtotime($staff["$field"]));
        }
            
        array_push($sidebar, "$staff[id] --  $staff[fname] --  $staff[lname] --  $staffdate");
      }
    }
  }

  if ($sidebar) {
    foreach ($sidebar as $i) {
      $employee = explode (' -- ', $i);
      $id = $employee[0];
      $fname = $employee[1];
      $lname = $employee[2];
      $day = $employee[3];
      echo "<li><a href='?page=profile&profileid=$id'>$fname $lname</a> $day</li>";
    }
  }
}


   ///////////////////
  //  LIMIT WORDS  //
 ///////////////////

function word_limit($string, $amnt) {
  $words = explode(" ", $string);
  return implode(" ", array_splice($words, 0, $amnt));
}


   ///////////////
  //  MESSAGE  //
 ///////////////

function message($message) {
  echo "<br /><div class='message'>$message</div><br />";
} 



   ////////////////////////////
  //  MULTIPAGE NAVIGATION  //
 ////////////////////////////

function multipageNav($limit, $total_rows) {
  global $_POST;
  global $_GET;
  global $page;
  $post = base64_encode(serialize($_POST)); //CONVERT POST ARRAY
  if (isset($_GET['offset'])) { $offset=$_GET['offset']; } else { $offset = 0; }

  $pages = $total_rows / $limit;            //FIND TOTAL PAGES NEEDED
  $pages_temp = round($pages);
  if ($pages > $pages_temp) { $pages = ($pages_temp + 1); } 

  if ($total_rows > $limit) {
    echo "<center>";
    if ($offset > 0) { 
      echo "<a href='?page=$page&offset=" . ($offset-$limit) . "&post=$post'>&laquo;Prev</a> &nbsp;"; 
    } else {
      echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    $i = 0;
    while ($pages > $i) {
      if (($offset/$limit)+1 == ($i+1)) {
        echo " " . ($i+1) . " ";
      } else {
        echo "&nbsp; <a href='?page=$page&offset=" . ($limit * $i) . "&post=$post'>" . ($i+1) . "</a> &nbsp;";
      }
      $i += 1;
    }
    
    if ($offset + $limit < $total_rows) { 
      echo "&nbsp; <a href='?page=$page&offset=" . ($offset+$limit) . "&post=$post'>Next&raquo;</a>"; 
    } else {
      echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }
    echo "</center>";
  }
}


   //////////////////////////////
  //  ARTICLE SECURITY CHECK  //
 //////////////////////////////

function hasPermission($type) {
  
  if ( isset($_SESSION['security']) ) {

    $permissions = array( // ----LIST AVAILABLE PERMISSIONS----
      'admin',            // All Permissions
      'approve',          // Approve or reject pending items
      'article',          // Submit articles
      'article_pend',     // Submit articles that require approval
      'comment',          // Submit comments
      'comment_pend',     // Submit comments that require approval
      'event',            // Submit events
      'event_pend',       // Submit events that require approval
      'help',             // Submit help articles
      'help_event',       // Submit help articles that require approval
      'job',              // Submit jobs
      'job_pend',         // Submit jobs that require approval
      'profile',          // Edit your profile
      'profile_pend'      // Edit your profile, changes require approval
    );

    $hasPerm = array();

    //Fill hasPerm array with values, TRUE or FALSE, for each permission
    
    foreach ($permissions as $i) {
      if (strstr($_SESSION['security'], "$i")) {
        $hasPerm["$i"] = TRUE;
      } else {
        $hasPerm["$i"] = FALSE;
      }
    }

    // If user has admin permission and the function call is not checking for '*_pend'
    // return true. Otherwise, return true if type is true in $hasPerm array.

    if ( $hasPerm['admin'] ) {
      if ( !strstr($type, '_pend') ) {
        return TRUE;
      }
    } elseif ( $hasPerm["$type"] ) {
      return TRUE;
    } else {
      return FALSE;
    }
    
  } 
  return FALSE;
}


   /////////////////
  //  ADD MYSQL  //
 /////////////////

function add_mysql($field) {
  foreach ($field as $i) {
    $mysql_text = "(";
    $mysql_text = $mysql_text . "$i, ";
  }
}



?>