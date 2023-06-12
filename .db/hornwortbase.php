<?php
// Create database connection
$eggnog = new SQLite3('eggnog.db');
$orthogroups = new SQLite3('orthogroups.db');
$orthogroupsrev = new SQLite3('orthogroups.reversed.db');
$expression = new SQLite3('expression.db');

// If URL variables are present
if (isset($_GET['transcript'])) {
  $transcript = SQLite3::escapeString($_GET['transcript']);
  if(!empty($transcript)){
    $query = "SELECT * FROM MAIN WHERE QUERY LIKE '$transcript'";
    $eggnog_results = $eggnog->query($query);
    $ogrev_results = $orthogroupsrev->query($query);
    $expression_results = $expression->query($query);
  }
 }
else if (isset($_GET['orthogroup'])) {
  $orthogroup = SQLite3::escapeString($_GET['orthogroup']);
  if(!empty($orthogroup)){
    $query = "SELECT * FROM MAIN WHERE ORTHOGROUP LIKE '$orthogroup'";
    $og_results = $orthogroups->query($query);
  }
}

// If search button is clicked ...
if (isset($_POST['search'])) {
  $transcript = SQLite3::escapeString($_POST['transcript']);
  $orthogroup = SQLite3::escapeString($_POST['orthogroup']);
  if(!empty($transcript)){//if keyword set goes here
    $query = "SELECT * FROM MAIN WHERE QUERY LIKE '$transcript'";
    $eggnog_results = $eggnog->query($query);
    $ogrev_results = $orthogroupsrev->query($query);
    $expression_results = $expression->query($query);
    }
  else if(!empty($orthogroup)) {
    $query = "SELECT * FROM MAIN WHERE ORTHOGROUP LIKE '$orthogroup'";
    $og_results = $orthogroups->query($query);
    }
}
echo "<pre>";
if (!empty($eggnog_results)){
  while($row = $eggnog_results->fetchArray()) {
    echo "Query: {$row['query']}<br>";
    echo "Seed ortholog: {$row['seed_ortholog']}<br>";
    echo "Evalue: {$row['evalue']}<br>";
    echo "Score: {$row['score']}<br>";
    echo "EggNOG OGs: {$row['eggNOG_OGs']}<br>";
    echo "Max Annotation Level: {$row['max_annot_lvl']}<br>";
    echo "COG Category: {$row['COG_category']}<br>";
    echo "Description: {$row['Description']}<br>";
    echo "Preferred Name: {$row['Preferred_name']}<br>";
    echo "GO Terms: {$row['GOs']}<br>";
    echo "EC: {$row['EC']}<br>";
    echo "KEGG ko: {$row['KEGG_ko']}<br>";
    echo "KEGG Pathway: {$row['KEGG_Pathway']}<br>";
    echo "KEGG Module: {$row['KEGG_Module']}<br>";
    echo "KEGG Reaction: {$row['KEGG_Reaction']}<br>";
    echo "KEGG rclass: {$row['KEGG_rclass']}<br>";
    echo "BRITE: {$row['BRITE']}<br>";
    echo "KEGG TC: {$row['KEGG_TC']}<br>";
    echo "CAZy: {$row['CAZy']}<br>";
    echo "BiGG Reaction: {$row['BiGG_Reaction']}<br>";
    echo "PFAMs: {$row['PFAMs']}<br>";
  }

while($row = $ogrev_results->fetchArray()){
  echo "Orthogroup: <a href=hornwortbase.php?orthogroup=" . $row['Orthogroup'] . ">" . $row['Orthogroup'] . "</a><br>";
  }

while($row = $expression_results->fetchArray()){
  echo "Gene expression<br>  FPKM: " . $row['FPKM'] . "<br>  TPM:  " . $row['TPM'] . "<br>  Relative level: " . $row['RelativeExpression'];
  }
echo "</pre>";
}

if (!empty($og_results)){
  $row = $og_results->fetchArray();
  echo "<pre>";
  foreach($row as $key => $value){
    if (!is_int($key)) {
      if (!empty($value)){
	if ($key == "Orthogroup"){
          echo "{$value} | <a href=fastas/{$value}.fa>SEQUENCES</a> | <a href=trees/{$value}_tree.txt>TREE</a>";
         }
        else {
          $seqArray = explode(',' , $value);
	  foreach($seqArray as $seqID){
            echo "<a href=hornwortbase.php?transcript=" . $seqID . ">" . $seqID . "</a>" . "\t";
            }
          }
        echo "<br>";
        }
      }
   }
}
echo "</pre>";

if (isset($_POST['descrip_search'])) {
  $description = SQLite3::escapeString($_POST['description']);
	if(!empty($description)){//if keyword set goes here
    $query = "SELECT * FROM MAIN WHERE PREFERRED_NAME LIKE '%$description%' OR DESCRIPTION LIKE '%$description%'";
    $description_results = $eggnog->query($query);
		if (!empty($description_results)){
			echo "<pre>";
			echo "<table>";
		  echo "<tr><th>Transcript ID</th><th>Gene Name</th><th>Gene Description</th></tr>";
		  while($row = $description_results->fetchArray()) {
				echo "<tr><td><a href=hornwortbase.php?transcript={$row['query']}>{$row['query']}</a></td><td>{$row['Preferred_name']}</td><td>{$row['Description']}</td></tr>";
			}
			echo "</table>";
			echo "</pre>";
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Image Search</title>
<style type="text/css">
   #content{
   	width: 50%;
   	margin: 20px auto;
   	border: 1px solid #cbcbcb;
   }
   form{
   	width: 50%;
   	margin: 20px auto;
   }
   form div{
   	margin-top: 5px;
   }
   #img_div{
   	width: 80%;
   	padding: 5px;
   	margin: 15px auto;
   	border: 1px solid #cbcbcb;
   }
   #img_div:after{
   	content: "";
   	display: block;
   	clear: both;
   }
   img{
    display: block;
    width:100%;
    max-width:600px;
    max-height:600px;
    width: auto;
    height: auto;
   }

</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
  <div id="content">
  <form method="post" action="hornwortbase.php" enctype="multipart/form-data">
      <b>Transcript ID</b>
      <div>
        <input type="text" name="transcript" placeholder="" value="<?php if (isset($_REQUEST['transcript'])) echo $_REQUEST['transcript']?>">
      </div>
      <br>
      <b>Orthogroup ID</b>
      <div>
        <input type="text" name="orthogroup" placeholder="" value="<?php if (isset($_REQUEST['orthogroup'])) echo $_REQUEST['orthogroup']?>">
      </div>
    <div>
    	<button type="submit" name="search">SEARCH</button>
    </div>
  </form>
  </div>
	<div id="content">
	<form method="post" action="hornwortbase.php" enctype="multipart/form-data">
			<b>Search gene functions by name/description</b>
			<div>
				<input type="text" name="description" placeholder="" value="<?php if (isset($_REQUEST['description'])) echo $_REQUEST['description']?>">
			</div>
		<div>
			<button type="submit" name="descrip_search">SEARCH</button>
		</div>
	</form>
	</div>
 <div>
  <a href="downloads.html">Download Files</a>
 </div>
 <div>
  <a href="blast.php">BLAST</a>
 </div>
</body>
</html>
