<!DOCTYPE html>


<?php
function verif_txt($sign,$cdt,$val) {
	if ($sign=="=") {
		$cdt=substr($cdt, 0, -1);
		$val= " LIKE '%$val%'";
	} else {
		if ($sign=="!=") {
			$cdt=substr($cdt, 0, -2);
			$val= " NOT LIKE '%$val%'";
		} else {
			exit("ERREUR : Le signe associé au champ $var ne peut être que 'Egal' ou 'Différent de'.");
		}
	}
	return array($cdt,$val);
}

function verif_com($sign,$cdt,$val) {
	$com=explode(" ",$val);
	$n=sizeof($com);

	if ($sign=="=") {
		$cdt=substr($cdt, 0, -1);
		$val= " LIKE '%".$com[0]."%'";
		if($n!=1) {
			for ($i=1;$i<$n;$i++) $val.= "AND (comments.comment LIKE '%".$com[$i]."%')";
		}
	} else {
		if ($sign=="!=") {
			$cdt=substr($cdt, 0, -2);
			$val= " NOT LIKE '%".$com[0]."%'";
			if($n!=1) {
				for ($i=1;$i<$n;$i++) $val.= "AND (comments.comment NOT LIKE '%".$com[$i]."%')";
			}
		} else {
			exit("ERREUR : Le signe associé au champ $var ne peut être que 'Egal' ou 'Différent de'.");
		}
	}
	return array($cdt,$val);
}

function verif_aut($sign,$cdt,$val) {
	if ($sign=="=") {
		$cdt=substr($cdt, 0, -1);
		$aut=explode(" ",$val);
		
		$n=sizeof($aut);
		$val=" LIKE '% ".$aut[0]."%' OR article.authors LIKE '".$aut[0]."%'";
		
		if($n!=1) {
			for ($i=1;$i<$n;$i++) $val.= "AND (article.authors LIKE '% ".$aut[$i]."%' OR article.authors LIKE '".$aut[$i]."%')";
		}
		
	} else {
		if ($sign=="!=") {
			$cdt=substr($cdt, 0, -2);
			$aut=explode(" ",$val);
		
			$n=sizeof($aut);
			$val=" NOT LIKE '% ".$aut[0]."%' OR article.authors NOT LIKE '".$aut[0]."%'";
			
			if($n!=1) {
				for ($i=1;$i<$n;$i++) $val.= "AND (article.authors NOT LIKE '% ".$aut[$i]."%' OR article.authors NOT LIKE '".$aut[$i]."%')";
			}
		} else {
			exit("ERREUR : Le signe associé au champ $var ne peut être que 'Egal' ou 'Différent de'.");
		}
	}
	return array($cdt,$val);
}

function verif_tit($sign,$cdt,$val) {
	$tit=explode(" ",$val);
	$n=sizeof($tit);

	if ($sign=="=") {
		$cdt=substr($cdt, 0, -1);
		$val= " LIKE '%".$tit[0]."%'";
		if($n!=1) {
			for ($i=1;$i<$n;$i++) $val.= "AND (article.title LIKE '%".$tit[$i]."%')";
		}
	} else {
		if ($sign=="!=") {
			$cdt=substr($cdt, 0, -2);
			$val= " NOT LIKE '%".$tit[0]."%'";
			if($n!=1) {
				for ($i=1;$i<$n;$i++) $val.= "AND (article.title NOT LIKE '%".$tit[$i]."%')";
			}
		} else {
			exit("ERREUR : Le signe associé au champ $var ne peut être que 'Egal' ou 'Différent de'.");
		}
	}
	return array($cdt,$val);
}



function verif_ec($sign,$cdt,$val) {
	if ($sign=="=") {
		$cdt=substr($cdt, 0, -1);
		$val= " REGEXP '[a-zA-Z]$val$' OR ec4='$val'";
	} else {
		if ($sign=="!=") {
			$cdt=substr($cdt, 0, -2);
			$val= " REGEXP '^[a-zA-Z]$val$' OR ec4='$val'";
		}
	}
	return array($cdt,$val);
}
?>

<html lang="fr">

	<head>
		<link rel="stylesheet" type="text/css" href="./form.css">

		<script type="text/javascript" charset="utf8" src="DataTables/jQuery-3.2.1/jquery-3.2.1.js"></script>
		<link rel="stylesheet" type="text/css" href="DataTables/datatables.css">
		<script type="text/javascript" charset="utf8" src="DataTables/datatables.js"></script>	
	</head>
	
	<body class = "principal">
		
		<?php
			session_start();
			$titre = "Recherche Avancée - Résultats";
			include("./includes/identifiants.php");
			include("./includes/debut.php");

			if ($id==0)	erreur(ERR_IS_NOT_CO.REDIRECT);
			else echo MENU;


			// REQUETE AVANCEE - CONSTRUCTION
			$cdt="";
			$select="";
			$q="";
			$ec=array();
					
			//~ echo var_dump($_POST);
			
			//~ $flag=0;
			
			$s1="SELECT DISTINCT enzyme.ec, enzyme.accepted_name, enzyme.systematic_name, synonym.synonyme, enzyme.activity, enzyme.cofactors, swissprot.num_swissprot, swissprot.code_swissprot, prosite.num_prosite, article.authors, article.title, article.year, article.volume, article.first_page, article.last_page, article.pubmed, article.medline, edition.editorial_place, edition.city, edition.edition, edition.editor, comments.comment, enzyme.history, note.note, note.type";
			$s2="SELECT DISTINCT enzyme.ec, enzyme.accepted_name, enzyme.systematic_name, synonym.synonyme, enzyme.activity, enzyme.cofactors, swissprot.num_swissprot,swissprot.code_swissprot, prosite.num_prosite, article.title, article.authors, article.year, edition.editorial_place,edition.city,edition.edition,edition.editor, comments.comment, note.type,note.note";
			
			$var_real_name=array();
			$db_var=array();
			$var_select=false;
			$var_flag=false;
			
			foreach($_POST as $key => $val) {
				if($val=="") exit("ERREUR : il y a des champs incomplets. Veuillez revoir votre requête.");
				// Création de la sélection sur gros tableau (clauses SELECT+FROM)
				if(preg_match("#^selection#", $key)) {					
					$select= "SELECT DISTINCT ";
					$var_select=true;
					
					$f=true;
					
					foreach($val as $i => $j) {
						$tmp=explode(";",$j);
						$j=$tmp[0];
						array_push($db_var,$j);
						array_push($var_real_name,$tmp[1]);
						if($f) {
							$f=false;
							$select.= $j;
						}
						else $select.= ", $j";
					}
					
					$save_select=$select;
					
					$select.=" FROM enzyme LEFT JOIN comments ON enzyme.id_enzyme=comments.id_enz LEFT JOIN note ON note.id_enzyme=enzyme.id_enzyme LEFT JOIN prosite ON prosite.id_enzyme=enzyme.id_enzyme LEFT JOIN swissprot ON swissprot.id_enzyme=enzyme.id_enzyme LEFT JOIN synonym ON synonym.id_enzyme=enzyme.id_enzyme LEFT JOIN publie ON publie.id_enzyme=enzyme.id_enzyme LEFT JOIN article ON article.id_article=publie.id_article LEFT JOIN edition ON edition.id_article=article.id_article";
					$select.=" WHERE ";
				}
				else {
					// Création des conditions de requete (clause WHERE)
					if(preg_match("#^list#", $key)) {
						if ($cdt == "") $cdt="(";
						else $cdt.=") AND (";
						
						$var=$val; // on garde en mémoire la variable d'intérêt pour tester par la suite la conformité des données founies 
						$or=true; // flag de condition OU			
						$cdt.=$val;
					}
					else {
						// On récupère les informations sur le signe
						if(preg_match("#^sign#", $key)) {
							if($or) {
								$or=false;
								$cdt.=$val;
							}
							else $cdt.= " OR $var$val";
							
							$sign=$val; // stockage du signe pour pouvoir modifier dans les requetes textuelles
						}
						else {
							if(preg_match("#^name#", $key)) {
								if($var=="enzyme.ec1") {
									if (!($val>0 & $val <= 6)) exit("ERREUR : EC1 doit être compris entre 1 et 6");
									array_push($ec,"ec1");
								}
								else {
									if ($var=="enzyme.ec2") {
										if (!($val>0)) exit("ERREUR : EC2 doit être un entier positif");
										array_push($ec,"ec2");
										//~ else {
											//~ if (in_array("ec1",$ec1)) array_push($ec,"ec2");
											//~ else exit("ERREUR : EC1 doit être renseigné si la requête porte sur EC2");
										//~ }
									} else {
										$var_flag=true;
										if ($var=="enzyme.ec3") {
											$var_flag=false;
											if (!($val>0)) exit("ERREUR : EC3 doit être un entier positif");								
											array_push($ec,"ec3");
											//~ if (in_array("ec1", $ec) && in_array("ec2", $ec)) array_push($ec,"ec3");
											//~ else exit("ERREUR : EC1 et EC2 doivent être renseignés si la requête porte sur EC3");	
										} else {
											if ($var=="enzyme.ec4") {
												if (!($val>0)) exit("ERREUR : EC4 doit être un entier positif");			
												array_push($ec,"ec4");
												$tmp=verif_ec($sign,$cdt,$val);
												$val=$tmp[1];
												$cdt=$tmp[0];
												//~ if (in_array("ec1", $ec) && in_array("ec2", $ec) && in_array("ec3", $ec)) $val=verif_ec($sign,$cdt,$val);
												//~ else exit("ERREUR : EC1, EC2 et EC3 doivent être renseignés si la requête porte sur EC4");
											}
											else {
												if($var=="article.year") {
													if (!($val>1900)) exit("ERREUR : L'année de publication est un entier supérieur à 1980");
												}
												else {
													if($var=="notes.type") {
														if (!($val=="deleted")&&!($val=="#transferred#")) exit("ERREUR : Les notes ne sont que de 2 types : \"deleted\" ou \"transferred\". Merci de préciser l'un des deux.");
													}
													else {
														if($var=="enzyme.accepted_name") {
															$tmp=verif_txt($sign,$cdt,$val);
															$val=$tmp[1];
															$cdt=$tmp[0];
														}
														else {
															if($var=="enzyme.systematic") {
																$tmp=verif_txt($sign,$cdt,$val);
																$val=$tmp[1];
																$cdt=$tmp[0];
															}
															else {
																if($var=="synonym.synonyme") {
																	$tmp=verif_txt($sign,$cdt,$val);
																	$val=$tmp[1];
																	$cdt=$tmp[0];
																}
																else {
																	if($var=="allname") $val=verif_txt($sign,$cdt,$val);
																	// fonction particuliere a ajouter
																	else {
																		if($var=="article.title") {
																			$tmp=verif_tit($sign,$cdt,$val);
																			$val=$tmp[1];
																			$cdt=$tmp[0];
																		}
																		else {
																			if($var=="article.authors") {
																				$tmp=verif_aut($sign,$cdt,$val);
																				$val=$tmp[1];
																				$cdt=$tmp[0];
																			}
																			else {
																				if($var=="comments.comment") {
																					$tmp=verif_com($sign,$cdt,$val);
																					$val=$tmp[1];
																					$cdt=$tmp[0];
																				}
																				else {
																					if($var=="enzyme.cofactors") {
																						$tmp=verif_txt($sign,$cdt,$val);
																						$val=$tmp[1];
																						$cdt=$tmp[0];
																					}
																					else if($var=="enzyme.activity") {
																						$tmp=verif_txt($sign,$cdt,$val);
																						$val=$tmp[1];
																						$cdt=$tmp[0];
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}	
										}
									}
								}
							}
							$cdt.=$val;
						}
					}
				}
			}
			$cdt.=");";
			$q=$select.$cdt;
			
			//~ echo $q;
			
			if(!($var_select)) exit("</br>ERREUR : Aucune variable d'intérêt sélectionnée. Veuillez modifier votre requête.");
			
			// Test de validité de la requete si elle implique des numéros EC
			if(!(empty($ec))) {
				
				// Si EC4 renseigné
				if (in_array("ec4",$ec)) {
					// Il doit y avoir EC3
					if (in_array("ec3",$ec)) {
						//Il doit y avoir EC2
						if (in_array("ec2",$ec)) {
							// Il doit y avoir EC1
							if (!(in_array("ec1",$ec))) exit("ERREUR : EC1, EC2 et EC3 doivent être renseignés si la requête porte sur EC4"); // il manque EC1
						}
						else exit("ERREUR : EC1, EC2 et EC3 doivent être renseignés si la requête porte sur EC4"); // il manque EC2
					}
					else exit("ERREUR : EC1, EC2 et EC3 doivent être renseignés si la requête porte sur EC4"); // il manque EC3
				}
				else {
					// Si EC3
					if (in_array("ec3",$ec)) {
						// EC2 ?
						if (in_array("ec2",$ec)) {
							// EC1 ?
							if (!(in_array("ec1",$ec))) exit("ERREUR : EC1 et EC2 doivent être renseignés si la requête porte sur EC3"); // pas d'EC1
						}
						else exit("ERREUR : EC1 et EC2 doivent être renseignés si la requête porte sur EC3"); // pas d'EC2
					}
					else {
						// Si EC2
						if (in_array("ec2",$ec)) {
							if (!(in_array("ec1",$ec))) exit("ERREUR : EC1 doit être renseigné si la requête porte sur EC2"); // pas d'EC1
							else {
								// S'il n'y a que EC1 et EC2, il ne faut pas que la requête soit trop grande
								$error_long_select="</br>ERREUR : Le résultats de la requête est trop grand. Veuillez la préciser ou réduire le nombre de variables d'intérêt";
								// S'il n'y a pas d'autre variable de requête que EC1 et EC2
								if(!($var_flag)) {
									if ($save_select==$s1) {
										exit($error_long_select);
									} else if ($save_select==$s2) exit($error_long_select);
								}
							}
						}
						// Que EC1		
						else {
							// S'il n'y a que EC1, il ne faut pas que la requête soit trop grande
							$error_long_select="</br>ERREUR : Le résultats de la requête est trop grand. Veuillez la préciser ou réduire le nombre de variables d'intérêt";
							// S'il n'y a pas d'autre variable de requête que EC1
							if(!($var_flag)) {
								if ($save_select==$s1) {
									exit($error_long_select);
								} else if ($save_select==$s2) exit($error_long_select);
							}				
						}
					}
				}
			}
			
			//REQUETE AVANCEE - AFFICHAGE DES RESULTATS
			$query=$db->query($q);			
			$res="";
			
			echo '<table id="traitementav_fr" class="display" width="100%" cellspacing="0"><thead><tr>';
			
			foreach($var_real_name as $key) {
				$tmp=explode(",",$key);
				$n=count($tmp);
				if ($n>1) for ($i=0;$i<$n;$i++) {
					if ($tmp[$i]!="Num Swissprot") echo '<th>'.$tmp[$i].'</th>';
				}
				else echo '<th>'.$key.'</th>';	
			}
			echo '</tr></thead><tbody>';
			
			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$tmp='<tr>';
				foreach ($row as $key => $val) {
					if($key=="ec") {
						$ec_nb=substr($val,3);
						$t='<td><a target="_blank" href="traitementfiche_fr.php?ec='.$val.'">'.$val.'</a></td>';					
						$tmp.=$t;						
					}
					else {
						if($key=="pubmed") {
							$t='<td><a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed/'.$val.'">'.$val.'</a></td>';
							$tmp.=$t;
						}
						else {
							if($key=="medline") {
								$t='<td><a target="_blank" href="https://www.ncbi.nlm.nih.gov/pubmed?cmd=PureSearch&term='.$ec_nb.'"[EC%2FRN Number]>'.$row['medline'].'</a></td>';
							$tmp.=$t;
							}
							else {
								if($key=="num_prosite") {
									$t='<td><a target="_blank" href="https://prosite.expasy.org/'.$val.'">'.$val.'</a></td>';
									$tmp.=$t;
								}
								else {
									if($key=="num_swissprot") $sw=$val;
									else {
										if($key=="code_swissprot") {
											$t='<td><a target="_blank" href="http://www.uniprot.org/uniprot/'.$sw.'">'.$val.'</a></td>';
											$tmp.=$t;
										}
										else { 
											$t='<td>'.$val.'</td>';					
											$tmp.=$t;
										}
									}
								}
							}
						}
					}
				}
				$tmp.='</tr>';
				echo $tmp;
			}
			echo '
				</tbody>
			</table>';

			echo "
		<script type=\"text/javascript\">
			$(document).ready(function() {
				$('#traitementav_fr').DataTable({
					dom: 'Bfrtip',
					lengthMenu: [
			            [ 10, 25, 50, -1 ],
			            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
			        ],
			        // columnDefs: [
			        //     {
			        //         targets: -1,
			        //         visible: false
			        //     } 
       				// ],

					buttons: [
					 	{
							extend: 'collection',
			                text: 'Export',
			                buttons: [
			                	{
					                extend: 'copyHtml5',
					                exportOptions: {
					                    columns: ':visible'
					                }
					            },
					            {
					                extend: 'csvHtml5',
					                exportOptions: {
					                    columns: ':visible'
					                }
					            },
					            {
					                extend: 'excelHtml5',
					                exportOptions: {
					                    columns: ':visible'
					                }
					            },
					            {
					                extend: 'pdfHtml5',
					                orientation: 'landscape',
               						pageSize: 'LEGAL',
					                exportOptions: {
					                    columns: ':visible'
					                }
					            },
					            {
					                extend: 'print',
					                exportOptions: {
					                    columns: ':visible'
					            	}
					            }
			                ]
						},
						'pageLength',
						{
							extend: 'colvis'
						}
					]
				});
			} );
		</script>";

			echo PIED;
		?>
	</body>
</html>


