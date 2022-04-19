<?php

namespace App\Controller;

use PDO;
use FPDF;
use PDOException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FPDFController extends AbstractController
{
    /**
     * @Route("/fpdf", name="app_fpdf")
     */
    public function index()
    {
        // require 'fpdf/fpdf.php';
        // require_once("../ctrl/init.ctrl.php");
        
        // $username=$_SESSION['user'];
        // $sql = executeRequete("SELECT * FROM membre WHERE prenom='$username'");
        // $client = $sql->fetch_assoc();
     
        // var_dump($username);
        // die;
        $sql = ("select  * from user WHERE nom='app.user.name'");
        $connectString = "mysql:host=localhost;dbname=symf_shop;charset=utf8";
        try {
        $bdd = new PDO($connectString, "root", "", 
                    array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)
                );
                return $bdd;
        }
        catch (PDOException $ex) {
            var_dump("Erreur GET COURSE: {$ex->getMessage()}");
        }	$req = $bdd->query($sql);
        $req->execute();
        $req->setFetchMode(PDO::FETCH_ASSOC);
        $client = $req->fetch();
    
        // var_dump($client);
        // die;
        if($req->rowCount() < 1){
            header('Location: #');
            echo "<script type='text/javascript'> document.location = 'gestion_commande.php'; </script>";
            exit;
        }
    
        //Debut PDF
        
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        
        $pdf->Image("images/Bitcoin.svg.png",10,6,18);
        $pdf->Ln(18);
        
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0, 6,"Store MVC", 0, 1);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0, 6, utf8_decode('67 69 Avenue du Général de Gaulle'), 0, 1);
        $pdf->Cell(0, 6, utf8_decode("Champs sur Marne, 77300, France"), 0, 1);
        $pdf->Cell(0, 6, utf8_decode('Tél : 01 23 56 89 56'), 0, 1);
        $pdf->Ln(8);
    
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(130, 6,'', 0, 0);
        // $pdf->Cell(59, 6, utf8_decode($client['civilite']) .' '. utf8_decode($client['prenom']).' '.utf8_decode($client['nom']), 0, 1);
        $pdf->Cell(59, 6, utf8_decode($client['prenom']).' '.(utf8_decode(strtoupper($client['nom']))), 0, 1);
    
        // $pdf->SetFont('Arial','',12);
        // $pdf->Cell(130, 6,'', 0, 0);
        // $pdf->Cell(59, 6, utf8_decode($client['adresse']), 0, 1);
    
        // $pdf->Cell(130, 6,'', 0, 0);
        // $pdf->Cell(59, 6, utf8_decode($client['code_postal']).' '.utf8_decode($client['ville']), 0, 1);
        // $pdf->Ln(8);
    
        $idCmd=$_GET['id_commande'];
    
        // $sqlQuery= executeRequete("select * from commande where id_commande='$idCmd'");
    
        $sql = ("select  * from commande WHERE id_commande='$idCmd'");
        $connectString = "mysql:host=localhost;dbname=tp_store;charset=utf8";
        try {
        $bdd = new PDO($connectString, "root", "", 
                    array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)
                );
                return $bdd;
        }
        catch (PDOException $ex) {
            var_dump("Erreur GET COURSE: {$ex->getMessage()}");
        }	$req = $bdd->query($sql);
        $req->execute();
        $req->setFetchMode(PDO::FETCH_ASSOC);
        // $client = $req->fetch();
    
            // while ($rowCmd = $sqlQuery->fetch_assoc()){
                foreach($req as $rowCmd){
                $pdf->SetFont('Arial','B',16);
                $pdf->cell(0,10, utf8_decode("Facture n°:"). " " . $rowCmd['id_commande'], 'TB', 1, 'C');
                $pdf->SetFont('Arial','',14);
                $pdf->Ln(8);
                $pdf->write(7, 'Le : '.strftime('%d-%m-%Y',strtotime($rowCmd['date_enregistrement']))."\n");
                
        }
        $pdf->Ln(4);
    
        $pdf->SetFont('Arial','B',14);
        $pdf->cell(90,6,utf8_decode("Désignation ") , 1, 0, 'C');
        $pdf->cell(25,6,utf8_decode("Qte ") , 1, 0, 'C');
        $pdf->cell(35,6,utf8_decode("Prix ") , 1, 0, 'C');
        $pdf->cell(40,6,utf8_decode("Total ") , 1, 1, 'C');
    
        $euro = chr(128);
        // $stmtLigneCmd = executeRequete("SELECT * FROM details_commande WHERE id_commande='$idCmd'");
    
        $sql = ("select  * from details_commande WHERE id_commande='$idCmd'");
        $connectString = "mysql:host=localhost;dbname=tp_store;charset=utf8";
        try {
        $bdd = new PDO($connectString, "root", "", 
                    array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)
                );
                return $bdd;
        }
        catch (PDOException $ex) {
            var_dump("Erreur GET COURSE: {$ex->getMessage()}");
        }	$req = $bdd->query($sql);
        $req->execute();
        $req->setFetchMode(PDO::FETCH_ASSOC);
        // $client = $req->fetch();
    
            // while ($rowCmd = $sqlQuery->fetch_assoc()){
                
                
                
                $MontantTotal = 0;
                // while ($rowtLigneCmd = $stmtLigneCmd->fetch_assoc()){    
            foreach($req as $rowtLigneCmd){
            //extract($rowtLigneCmd);
            $pdf->SetFont('Arial','',12);
            //Designation
            $pdf->cell(90,6,utf8_decode(ucfirst($rowtLigneCmd['designation'])) , 1, 0);
            //quantite
            $pdf->cell(25,6,$rowtLigneCmd['quantite'], 1, 0);
            //prix
            $pdf->cell(35,6,$rowtLigneCmd['prix'].' '. $euro, 1, 0);
            //prix total par article
            $prixT = $rowtLigneCmd['quantite'] * $rowtLigneCmd['prix'];
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
        $pdf->cell(0,10, utf8_decode("© 2022 Copyright: Store_MVC - Tous droits reservés."), 'TB', 1, 'C');
        $pdf->Output();
    
        
    
    
    }
}
