<?php
//echo "<pre>";
//var_dump($_POST);
if (isset($_POST['blast'])) {
  $sequence = SQLite3::escapeString($_POST['sequence']);
  $seqtype = SQLite3::escapeString($_POST['seqtype']);
  $db = SQLite3::escapeString($_POST['db']);
//  echo "{$sequence} <br> {$seqtype} <br> {$db}";
  $tmpfile = tmpfile();
  $tmpfile_path = stream_get_meta_data($tmpfile)['uri'];
  fwrite($tmpfile, $sequence);
//  fseek($tmpfile, 0);
//  echo fread($tmpfile, filesize($tmpfile_path));
//echo "</pre>";

  if ($db == "all"){ $dbfile = "./blast/All_hornworts.{$seqtype}"; }
  else if ( $db == "anagrbonn"){ $dbfile = "./blast/Anthoceros_agrestis_Bonn.{$seqtype}"; }
  else if ( $db == "anagroxf") {$dbfile = "./blast/Anthoceros_agrestis_Oxford.{$seqtype}"; }
  else if ( $db == "anfus") {$dbfile = "./blast/Anthoceros_fusiformis.{$seqtype}"; }
  else if ( $db == "anpun") {$dbfile = "./blast/Anthoceros_punctatus.{$seqtype}"; }
  else if ( $db == "ledus") {$dbfile = "./blast/Leiosporoceros_dussii.{$seqtype}"; }
  else if ( $db == "mefla") {$dbfile = "./blast/Megaceros_flagellaris.{$seqtype}"; }
  else if ( $db == "noorb") {$dbfile = "./blast/Notothylas_orbicularis.{$seqtype}"; }
  else if ( $db == "papea") {$dbfile = "./blast/Paraphymatoceros_pearsonii.{$seqtype}"; }
  else if ( $db == "phcar") {$dbfile = "./blast/Phaeoceros_carolinianus.{$seqtype}"; }
  else if ( $db == "phsp")  {$dbfile = "./blast/Phaeoceros_sp.{$seqtype}"; }
  else if ( $db == "phfim") {$dbfile = "./blast/Phaeomegaceros_fimbriatus.{$seqtype}"; }
  else if ( $db == "phphy") {$dbfile = "./blast/Phymatoceros_phymatodes.{$seqtype}"; }
 
  if ($seqtype == "nucl") { $blastcmd = "blastn"; }
  else if ($seqtype == "prot") { $blastcmd = "blastp"; }

  $commandline = "{$blastcmd} -query {$tmpfile_path} -db {$dbfile} -outfmt 6 -evalue 1e-5 -num_threads 8";
  $output = shell_exec($commandline);
//  echo $commandline;
  echo "<table>";
  echo "<tr><th>Query</th><th>Target</th><th>% identical positions</th><th>Alignment Length</th><th>Mismatches</th><th>Gaps</th><th>Query Start</th><th>Query Stop</th><th>Target Start</th><th>Target Stop</th><th>Evalue</th><th>Bitscore</th></tr>";
  $outputArr = explode("\n", $output);
  foreach($outputArr as $line){
    if(!empty($line)){
      $item = explode("\t", $line);
      echo "<tr><td><a href=hornwortbase.php?transcript={$item[0]}>{$item[0]}</a></td><td><a href=hornwortbase.php?transcript={$item[1]}>{$item[1]}</a></td>";
      echo "<td>{$item[2]}</td><td>{$item[3]}</td><td>{$item[4]}</td><td>{$item[5]}</td><td>{$item[6]}</td><td>{$item[7]}</td><td>{$item[8]}</td><td>{$item[9]}</td><td>{$item[10]}</td><td>{$item[11]}</tr>";
    }
  }
  echo "</table>"; 
//  var_dump($outputArr);
  echo "</pre>";

  // close the tmp file with input seq(s)
  fclose($tmpfile);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Image Search</title>
<style type="text/css">
   content{
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
   img_div{
   	width: 80%;
   	padding: 5px;
   	margin: 15px auto;
   	border: 1px solid #cbcbcb;
   }
   img_div:after{
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
   input,
   select,
   textarea {
    max-width: 1000px;
   }
   table,
   table td {
    border: 1px solid #cccccc;
   }
   td {
    text-align: center;
    vertical-align: middle;
   }
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<div id="content" align="center">
<h3>BLAST search of hornwort genes</h3>
<p>Input sequence(s) in FASTA format.</p>
</div>
<body>
  <div>
  <form method="post" action="blast.php" enctype="multipart/form-data">
   <textarea style="width: 750px; height: 250px;" name="sequence" rows="12" columns="35" required ></textarea> 
   <br>
   <label for="seq-select">Choose sequence type:</label>
   <select name="seqtype" id="seq-select">
    <option value="nucl">Nucleotide</option>
    <option value="prot">Amino Acid</option>
   </select>
   <br> 
   <label for="db-select">Choose search DB:</label>
   <select name="db" id="db-select">
     <option value="all">All hornwort genes</option>
     <option value="anagrbonn">Anthoceros agrestis Bonn</option>
     <option value="anagroxf">Anthoceros agrestis Oxford</option>
     <option value="anfus">Anthoceros fusiformis</option>
     <option value="anpun">Anthoceros punctatus</option>
     <option value="ledus">Leiosporoceros dussii</option>
     <option value="mefla">Megaceros flagellaris</option>
     <option value="noorb">Notothylas orbicularis</option>
     <option value="papea">Paraphymatoceros pearsonii</option>
     <option value="phcar">Phaeoceros carolinianus</option>
     <option value="phsp">Phaeoceros sp.</option>
     <option value="phfim">Phaeomegaceros fimbriatus</option>
     <option value="phphy">Phymatoceros phymatodes</option>
   </select>
   <div align="center">
     <button type="submit" name="blast">SUBMIT</button>
   </div>
 </form>
 </div>
 <div>
  <a href="hornwortbase.php">Main</a>
 </div>
 <div>
  <a href="downloads.html">Downloads</a>
 </div>
</body>
</html>

