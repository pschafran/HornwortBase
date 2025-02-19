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
    $trans = shell_exec("getFromFasta.py ./fastas/All_hornworts_TRANS.fasta {$transcript} 2>&1");
    $cds = shell_exec("getFromFasta.py ./fastas/All_hornworts_CDS.fasta {$transcript} 2>&1");
    $prot = shell_exec("getFromFasta.py ./fastas/All_hornworts_PROT.fasta {$transcript} 2>&1");
    $annot = shell_exec("grep -w {$transcript} ./gffs/All_hornworts_gene_annotations.gff 2>&1");
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
    $trans = shell_exec("getFromFasta.py ./fastas/All_hornworts_TRANS.fasta {$transcript} 2>&1");
    $cds = shell_exec("getFromFasta.py ./fastas/All_hornworts_CDS.fasta {$transcript} 2>&1");
    $prot = shell_exec("getFromFasta.py ./fastas/All_hornworts_PROT.fasta {$transcript} 2>&1");
    $annot = shell_exec("grep -w {$transcript} ./gffs/All_hornworts_gene_annotations.gff 2>&1");
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
  echo "Gene expression<br>  FPKM: " . $row['FPKM'] . "<br>  TPM:  " . $row['TPM'] . "<br>  Relative level: " . $row['RelativeExpression'] . "<br>";
  }
echo '<br><div class="seq">Transcript sequence (with introns):';
echo "<p>{$trans}</p></div>";
echo '<div class="seq">CDS sequence (without introns):';
echo "<p>{$cds}</p></div>";
echo '<div class="seq">Translated CDS sequence:';
echo "<p>{$prot}</p></div>";
echo "<br>Gene Features:";
echo "<table>";
echo "<tr><th>Seqname</th><th>Source</th><th>Feature</th><th>Start</th><th>End</th><th>Score</th><th>Strand</th><th>Frame</th><th>Attributes</th></tr>";
$annotArr = explode("\n", $annot);
  foreach($annotArr as $line){
    if(!empty($line)){
      $item = explode("\t", $line);
      echo "<tr><td>{$item[0]}</td><td>{$item[1]}</td><td>{$item[2]}</td><td>{$item[3]}</td><td>{$item[4]}</td><td>{$item[5]}</td><td>{$item[6]}</td><td>{$item[7]}</td><td>{$item[8]}</td></tr>";
    }
  }
echo "</table>";
echo "</pre>";
}

if (!empty($og_results)){
  $row = $og_results->fetchArray();
  foreach($row as $key => $value){
    if (!is_int($key)) {
      if (!empty($value)){
	if ($key == "Orthogroup"){
          echo "{$value} | <a href=fastas/{$value}.fa>SEQUENCES</a> | <a href=alignments/{$value}.CLUSTAL.fa>ALIGNMENT</a> | <a href=alignments_trimmed/{$value}.CLUSTAL.TRIM.fa>TRIMMED ALIGNMENT</a> | <a href=trees/{$value}_tree.txt>TREE</a>";
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
		echo "<tr><th>Transcript ID</th><th>Orthogroup</th><th>Gene Name</th><th>Gene Description</th></tr>";
		// loop through each line searching reverse orthogroup db for each transcript name
		while($row = $description_results->fetchArray()) {
				$transcript = SQLite3::escapeString($row['query']);
				$og_query = "SELECT * FROM MAIN WHERE QUERY LIKE '$transcript'";
				echo "<tr><td><a href=hornwortbase.php?transcript={$row['query']}>{$row['query']}</a></td>";
				$ogrev_results = $orthogroupsrev->query($og_query);
				// check if the results from the db search are empty (if transcript is missing from db, still returns true)
				if (!empty(($ogrev_results->fetchArray()))) {
					// reset to brginning of db to check again for missing values
					$ogrev_results->reset();
					// parse results from reverse orthogroup db search
					while($row2 = $ogrev_results->fetchArray()) {
						// catch any other rsults missing an orthogroup value
  						if (!empty($row2[1])){
							echo "<td><a href=hornwortbase.php?orthogroup=" . $row2['Orthogroup'] . ">" . $row2['Orthogroup'] . "</a></td>";
							}
						else {
							echo "<td>-</td>";
							}
						}
					}
				else {
					echo "<td>-</td>";
					}
				// close db results and unset variables to make sure they aren't carried to next iteration of loop
				$ogrev_results->finalize();
				unset($ogrev_results);
				unset($row2);
				echo "<td>{$row['Preferred_name']}</td><td>{$row['Description']}</td></tr>";
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
<title>HornwortBase</title>
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
   div{
   }
   .seq{
    max-width: 80ch;
    word-break: break-word;
    white-space: pre-wrap;
   }
   p{
   }
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
 <div id="content" align="center">
  <a href="downloads.html">Download Files</a> | <a href="blast.php">BLAST</a> | <a href="extract.php">Extract sequence</a>
 </div>
  <div id="content" align="center">
  <form method="post" action="hornwortbase.php" enctype="multipart/form-data">
    <p>
      <b>Transcript ID</b>
      <input type="text" name="transcript" placeholder="" value="<?php if (isset($_REQUEST['transcript'])) echo $_REQUEST['transcript']?>">
    </p>
    <p>
      <b>Orthogroup ID</b>
      <input type="text" name="orthogroup" placeholder="" value="<?php if (isset($_REQUEST['orthogroup'])) echo $_REQUEST['orthogroup']?>">
    </p>
    <p>
    	<button type="submit" name="search">SEARCH</button>
    </p>
  </form>
  </div>
  <div id="content" align="center">
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
 <div id="content" align="center">
  <a href="downloads.html">Download Files</a> |  <a href="blast.php">BLAST</a> |  <a href="extract.php">Extract sequences</a>
 </div>
</body>
</html>
