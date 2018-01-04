<?php
function erreur($err='')
{
   $mess=($err!='')? $err:'Une erreur inconnue s\'est produite';
   exit('<p>'.$mess.'</p>');
}



// Affichage des informations pertinentes dans le tableau en fonction de la checkbox (display_tab)
// IN : la query SQL
// OUT : $file pour l'export (+ echo du tableau sur la page)
function echo_resultats_sp($query){	
	$file="";
	echo '<table id="traitementsp_fr" class="display">
			<thead>
				<tr>';
	// Noms des colonnes du tableau
	// Si la box "tout" est cochée ou si aucune ne sont cochées, affiche toutes les colonnes
	if(in_array("ch_all", $_REQUEST['display_tab']) || empty($_REQUEST['display_tab'])) {
		echo '
		<th>EC Number</th>
		<th>Systematic Names</th>
		<th>Accepted Names</th>
		<th>Synonyms</th>
		<th>Cofactors</th>
		<th>Activity</th>
		<th>History</th>	
	</tr>
	<tbody>';

	$file=$file."EC Number\tSystematic Names\tAccepted Names\tSynonyms\tCofactors\tActivity\tHistory\n";
	}
	// Sinon teste quelles box sont cohées et display la colonne en conséquence
	else{
		if(in_array("ch_ec", $_REQUEST['display_tab'])) {
			echo '<th>EC Number</th>';
			$file=$file."EC Number\t";}	
		if(in_array("ch_systematic", $_REQUEST['display_tab'])) {
			echo '<th>Systematic Names</th>';
			$file=$file."Systematic Names\t";}
		if(in_array("ch_accepted", $_REQUEST['display_tab'])) {
			echo '<th>Accepted Names</th>';
			$file=$file."Accepted Names\t";}
		if(in_array("ch_synonym", $_REQUEST['display_tab'])) {
			echo '<th>Synonyms</th>';
			$file=$file."Synonyms\t";}
		if(in_array("ch_cofactors", $_REQUEST['display_tab'])) {
			echo '<th>Cofactors</th>';
			$file=$file."Cofactors\t";}
		if(in_array("ch_activity", $_REQUEST['display_tab'])) {
			echo '<th>Activity</th>';	
			$file=$file."Activity\t";}
		if(in_array("ch_history", $_REQUEST['display_tab'])) {
			echo '<th>History</th>';
			$file=$file."History\t";}
		$file=$file."\n";
		echo ' </tr>
		<tbody>';
	}
	   						
	// Test pour chaque ligne ce qu'il faut afficher en fonction de la checkbox (display_tab)
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		echo '<tr>';
		if(in_array("ch_all", $_REQUEST['display_tab']) || empty($_REQUEST['display_tab'])) {
			echo '<td>'.$row['ec'].'</td>
			   <td>'.$row['systematic_name'].'</td>
			   <td>'.$row['accepted_name'].'</td>
			   <td>'.$row['synonyme'].'</td>
			   <td>'.$row['cofactors'].'</td>
			   <td>'.$row['activity'].'</td>
			   <td>'.$row['history'].'</td>';

			$file=$file.$row['ec']."\t".$row['accepted_name']."\t".$row['systematic_name']."\t".$row['synonyme']."\t".$row['cofactors']."\t".$row['activity']."\t".$row['history'];
		}
		else{
			if(in_array("ch_ec", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['ec'].'</td>';
				$file=$file.$row['ec']."\t";
			}
			if(in_array("ch_systematic", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['systematic_name'].'</td>';
				$file=$file.$row['systematic_name']."\t";
			}
			if(in_array("ch_accepted", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['accepted_name'].'</td>';
				$file=$file.$row['accepted_name']."\t";
			}
			if(in_array("ch_synonym", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['synonyme'].'</td>';
				$file=$file.$row['synonyme']."\t";
			}
			if(in_array("ch_cofactors", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['cofactors'].'</td>';
				$file=$file.$row['cofactors']."\t";
			}
			if(in_array("ch_activity", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['activity'].'</td>';
				$file=$file.$row['activity']."\t";
			}
			if(in_array("ch_history", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['history'].'</td>';
				$file=$file.$row['history']."\t";
			}
			$file=$file."\n";
		}
		   echo '</tr>';			
	}
	echo '</tbody>
	</table>';

	echo "
		<script type=\"text/javascript\">
			$(document).ready(function() {
				$('#traitementsp_fr').DataTable();
			} );
		</script>";

	return $file;
}




// Affichage des informations pertinentes dans le tableau en fonction de la checkbox (display_tab)
// IN : la query SQL
// OUT : $file pour l'export (+ echo du tableau sur la page)
function echo_resultats_bib($query){	
	$file="";
	$pb="https://www.ncbi.nlm.nih.gov/pubmed/";
	echo '<table id="traitementbib_fr" class="display">
			<thead>
				<tr>';
	// Noms des colonnes du tableau
	// Si la box "tout" est cochée ou si aucune ne sont cochées, affiche toutes les colonnes
	if(in_array("ch_all", $_REQUEST['display_tab']) || empty($_REQUEST['display_tab'])) {
		echo '
		<th>EC Number</th>
		<th>Auteur</th>
		<th>Titre</th>
		<th>Année</th>
		<th>Volume</th>
		<th>Première page</th>
		<th>Dernière Page</th>
		<th>Pubmed</th>		
		<th>Medline</th>
	</tr>
	<tbody>';

	$file=$file."EC Number\tAuteur\tTitre\tAnnée\tVolume\tPremière page\tDernière page\tPubmed\tMedline\n";
	}
	// Sinon teste quelles box sont cohées et display la colonne en conséquence
	else{
		if(in_array("ch_ec", $_REQUEST['display_tab'])) {
			echo '<th>EC Number</th>';
			$file=$file."EC Number\t";}	
		if(in_array("ch_author", $_REQUEST['display_tab'])) {
			echo '<th>Auteur</th>';
			$file=$file."Auteur\t";}
		if(in_array("ch_title", $_REQUEST['display_tab'])) {
			echo '<th>Titre</th>';
			$file=$file."Titre\t";}
		if(in_array("ch_year", $_REQUEST['display_tab'])) {
			echo '<th>Année</th>';
			$file=$file."Année\t";}
		if(in_array("ch_volume", $_REQUEST['display_tab'])) {
			echo '<th>Volume</th>';
			$file=$file."Volume\t";}
		if(in_array("ch_fpage", $_REQUEST['display_tab'])) {
			echo '<th>Première page</th>';	
			$file=$file."Première page\t";}
		if(in_array("ch_lpage", $_REQUEST['display_tab'])) {
			echo '<th>Dernière page</th>';
			$file=$file."Dernière page\t";}
		if(in_array("ch_pubmed", $_REQUEST['display_tab'])) {
			echo '<th>Pubmed</th>';
			$file=$file."Pubmed\t";}
		if(in_array("ch_medline", $_REQUEST['display_tab'])) {
			echo '<th>Medline</th>';
			$file=$file."Medline\t";}
		$file=$file."\n";
		echo ' </tr>
		<tbody>';
	}
			
	// Test pour chaque ligne ce qu'il faut afficher en fonction de la checkbox (display_tab)
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		echo '<tr>';
		if(in_array("ch_all", $_REQUEST['display_tab']) || empty($_REQUEST['display_tab'])) {
			echo '
				<td>'.$row['ec'].'</td>
				<td>'.$row['authors'].'</td>
				<td>'.$row['title'].'</td>
			 	<td>'.$row['year'].'</td>
			 	<td>'.$row['volume'].'</td>
			 	<td>'.$row['first_page'].'</td>
			 	<td>'.$row['last_page'].'</td>
				<td><a target="_blank" href="'.$pb.$row['pubmed'].'">'.$row['pubmed'].'</a></td>
				<td>'.$row['medline'].'</td>';

			$file=$file.$row['ec']."\t".$row['authors']."\t".$row['title']."\t".$row['year']."\t".$row['volume']."\t".$row['first_page']."\t".$row['last_page']."\t".$row['pubmed'].$row['medline'];
		}
		else{
			if(in_array("ch_ec", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['ec'].'</td>';
				$file=$file.$row['ec']."\t";
			}
			if(in_array("ch_author", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['authors'].'</td>';
				$file=$file.$row['authors']."\t";
			}
			if(in_array("ch_title", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['title'].'</td>';
				$file=$file.$row['title']."\t";
			}
			if(in_array("ch_year", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['year'].'</td>';
				$file=$file.$row['year']."\t";
			}
			if(in_array("ch_volume", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['volume'].'</td>';
				$file=$file.$row['volume']."\t";
			}
			if(in_array("ch_fpage", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['first_page'].'</td>';
				$file=$file.$row['first_page']."\t";
			}
			if(in_array("ch_lpage", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['last_page'].'</td>';
				$file=$file.$row['last_page']."\t";
			}
			if(in_array("ch_pubmed", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['pubmed'].'</td>';
				$file=$file.$row['pubmed']."\t";
			}
			if(in_array("ch_medline", $_REQUEST['display_tab'])) {
				echo '<td>'.$row['medline'].'</td>';
				$file=$file.$row['medline']."\t";
			}
			$file=$file."\n";
		}
		   echo '</tr>';			
	}
	echo '</tbody>
	</table>';

	echo "
		<script type=\"text/javascript\">
			$(document).ready(function() {
				$('#traitementbib_fr').DataTable();
			} );
		</script>";

	return $file;
}



?>
