<?php
/**
 * @Route("/app_fpdf",name="app_fpdf")
 *  */ 
    require 'fpdf/fpdf.php';
    // require_once("../ctrl/init.ctrl.php");
    //--------- BDD
$mysqli = new mysqli("localhost", "root", "", "symf_shop");
if ($mysqli->connect_error) die('Un problème est survenu lors de la tentative de connexion à la BDD : ' . $mysqli->connect_error);
// $mysqli->set_charset("utf8");
 
//--------- SESSION
session_start();

//--------- CHEMIN
define("RACINE_SITE","http://localhost:8000/symf_shop/");
 
//--------- VARIABLES
$contenu = '';
 
//--------- AUTRES ctrlLUSIONS
// require_once("fonction.ctrl.php");
function executeRequete($req)
{
	global $mysqli; 
	$resultat = $mysqli->query($req); 
	if (!$resultat)
	{
		die("Erreur sur la requete sql.<br />Message : " . $mysqli->error . "<br />Code: " . $req);
	}
	return $resultat;
}
//------------------------------------
function debug($var, $mode = 1) 
{
		echo '<div style="background: orange; padding: 5px; float: right; clear: both; ">';
		$trace = debug_backtrace(); 
		$trace = array_shift($trace);
		echo "Debug demandé dans le fichier : $trace[file] à la ligne $trace[line].<hr />";
		if($mode === 1)
		{
			echo "<pre>"; print_r($var); echo "</pre>";
		}
		else
		{
			echo "<pre>"; var_dump($var); echo "</pre>";
		}
	echo '</div>';
}
//------------------------------------
function internauteEstConnecte()
{  
	if(!isset($_SESSION['membre'])) 
	{
		return false;
	}
	else
	{
		return true;
	}
}
//------------------------------------
function internauteEstConnecteEtEstAdmin()
{ 
	if(internauteEstConnecte() && $_SESSION['membre']['statut'] == 'admin') 
	{
			return true;
	}
	return false;
}

function creationDuPanier()
{
   if (!isset($_SESSION['panier']))
   {
      $_SESSION['panier']=array();
      $_SESSION['panier']['titre'] = array();
      $_SESSION['panier']['id_produit'] = array();
      $_SESSION['panier']['quantite'] = array();
      $_SESSION['panier']['prix'] = array();
	  $_SESSION['panier']['photo'] = array();
   }
}

function ajouterProduitDansPanier($titre,$id_produit,$quantite,$prix,$photo)
{
	creationDuPanier(); 
    $position_produit = array_search($id_produit,  $_SESSION['panier']['id_produit']); 
    if ($position_produit !== false)
    {
         $_SESSION['panier']['quantite'][$position_produit] += $quantite ;
    }
    else 
    {
        $_SESSION['panier']['titre'][] = $titre;
        $_SESSION['panier']['id_produit'][] = $id_produit;
        $_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['prix'][] = $prix;
		$_SESSION['panier']['photo'][] = $photo;
    }
}
//------------------------------------
function montantTotal()
{
   $total=0;
   for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++) 
   {
      $total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
   }
   return round($total,2);
}
//------------------------------------
function retirerproduitDuPanier($id_produit_a_supprimer)
{
	$position_produit = array_search($id_produit_a_supprimer,  $_SESSION['panier']['id_produit']);
	if ($position_produit !== false)
    {
		array_splice($_SESSION['panier']['titre'], $position_produit, 1);
		array_splice($_SESSION['panier']['id_produit'], $position_produit, 1);
		array_splice($_SESSION['panier']['quantite'], $position_produit, 1);
		array_splice($_SESSION['panier']['prix'], $position_produit, 1);
	}
}

// var_dump($_SESSION["_sf2_attributes"]["_security.last_username"]);
// die;
    
    $username=$_SESSION["_sf2_attributes"]["_security.last_username"];
	$sql = executeRequete("SELECT * FROM user WHERE email='$username'");
    $client = $sql->fetch_assoc();
 

	if($sql->num_rows < 1){
		header('Location: #');
		echo "<script type='text/javascript'> document.location = 'gestion_commande.php'; </script>";
		exit;
	}

    //Debut PDF
    
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    
    // $pdf->Image(RACINE_SITE.'ctrl/img/logo2.png',10,6,18);
    $pdf->Ln(18);
    
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0, 6,"e_shop", 0, 1);
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0, 6, utf8_decode('67 69 Avenue du Général de Gaulle'), 0, 1);
    $pdf->Cell(0, 6, utf8_decode("Champs sur Marne, 77300, France"), 0, 1);
    $pdf->Cell(0, 6, utf8_decode('Tél : 01 23 56 89 56'), 0, 1);
    $pdf->Ln(8);

    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(130, 6,'', 0, 0);
    // $pdf->Cell(59, 6, utf8_decode($client['civilite']) .' '. utf8_decode($client['prenom']).' '.utf8_decode($client['nom']), 0, 1);
    $pdf->Cell(59, 6, utf8_decode($client['first_name']).' '.utf8_decode($client['name']), 0, 1);

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(130, 6,'', 0, 0);
    // $pdf->Cell(59, 6, utf8_decode($client['adresse']), 0, 1);

    $pdf->Cell(130, 6,'', 0, 0);
    // $pdf->Cell(59, 6, utf8_decode($client['code_postal']).' '.utf8_decode($client['ville']), 0, 1);
    $pdf->Ln(8);

    // var_dump($_SESSION["_sf2_meta"]);
    // die;
    // $idCmd=$_GET['id_commande'];
    $idCmd=1;

    $sqlQuery= executeRequete("SELECT * FROM `order` WHERE 1 ");
		while ($rowCmd = $sqlQuery->fetch_assoc()){
            $pdf->SetFont('Arial','B',16);
            $pdf->cell(0,10, utf8_decode("Facture n°:"). " " . $rowCmd['id'], 'TB', 1, 'C');
            $pdf->SetFont('Arial','',14);
            $pdf->Ln(8);
            $pdf->write(7, 'Le : '.strftime('%d-%m-%Y',strtotime($rowCmd['date']))."\n");
            
    }
    $pdf->Ln(4);

    $pdf->SetFont('Arial','B',14);
    $pdf->cell(90,6,utf8_decode("Désignation ") , 1, 0, 'C');
    $pdf->cell(25,6,utf8_decode("Qte ") , 1, 0, 'C');
    $pdf->cell(35,6,utf8_decode("Prix ") , 1, 0, 'C');
    $pdf->cell(40,6,utf8_decode("Total ") , 1, 1, 'C');

    $euro = chr(128);
    $stmtLigneCmd = executeRequete("SELECT * FROM order_line WHERE order_num_id='$idCmd'");
    $MontantTotal = 0;
    while ($rowtLigneCmd = $stmtLigneCmd->fetch_assoc()){    
        //extract($rowtLigneCmd);
        $pdf->SetFont('Arial','',12);
        //Designation
        $pdf->cell(90,6,utf8_decode(ucfirst($rowtLigneCmd['product_id'])) , 1, 0);
        //quantite
        $pdf->cell(25,6,$rowtLigneCmd['quantity'], 1, 0);
        //prix
        $pdf->cell(35,6,$rowtLigneCmd['total'].' '. $euro, 1, 0);
        //prix total par article
        $prixT = $rowtLigneCmd['quantity'] * $rowtLigneCmd['total'];
        $pdf->cell(40,6,$prixT .' '. $euro , 1, 1);
        //On ajoute le total de la ligne au montant total
        $MontantTotal = $MontantTotal + $prixT;
    }
    // Recapitulatif

    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(115,6, '',0,0);
    $pdf->Cell(35,6, 'Prix total ',1,0);
    $pdf->Cell(40,6, $MontantTotal .' '. $euro ,1,1);
    $pdf->Ln(133);
    $pdf->SetFont('Arial','B',16);
    $pdf->cell(0,10, utf8_decode("© 2022 Copyright: E_SHOP - Tous droits reservés."), 'TB', 1, 'C');
    $pdf->Output();

    


?>