Login with an SSH tunnel to port 8080:
```
ssh -L 8080:localhost:8080 <your-username>@132.236.41.201
```
Then open `http://localhost:8080/hornwortbase.php` in a web browser. You should see a page where you can enter a transcript ID from any of the new genomes (old ones to be added later), or an 
orthogroup number (based on a new orthofinder analysis also released here).
If you search by transcript, it will return the functional annotation data for that transcript (if blank, then no info is present). You'll also get a link to the orthogroup that transcript is found 
in (if applicable).
The orthogroup page show all sequences in that orthogroup, with hornwort genome links back to their functional annotations (other links not active for now). At the top of the page are links to the 
FASTA sequence file for the orthogroup, and its fasttree phylogeny.
At the bottom of the page there's a link to a separate downloads page with links to the major files for each new genome that can be downloaded directly.

<img src="images/Screenshot_2023-04-26_at_11.05.40_AM.png">
<img src="images/Screenshot_2023-04-26_at_11.06.13_AM.png">
<img src="images/Screenshot_2023-04-26_at_11.06.46_AM.png">
<img src="images/Screenshot_2023-04-26_at_11.07.03_AM.png">
Update: there's now a basic BLAST function to search the hornwort genes. Query/targets link to the functional annotation and orthogroup databases.
<img src="images/Screenshot_2023-04-26_at_11.02.27_PM.png">
Small update: our local hornwortbase now shows expression values for every transcript
<img src="images/Screenshot_2023-05-07_at_9.47.53_PM.png">
Another local HWB update. Added a free-text search function you can use to search by gene names/descriptions. Works with partial matches and not case sensitive.  On the main page (hornwortbase.php) 
there's a new search box. Links in the results table go to the full functional annotation page for the transcript, which you can then click through to the orthogroup and sequences. All species used 
in the Viridiplantae orthogroups are searched, but hornworts appear at the top of the table.
 <img src="images/Screenshot_2023-05-18_at_12.18.40_PM.png">
<img src="images/Screenshot_2023-05-18_at_12.18.28_PM.png">


