<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 26/01/2017
 * Time: 21:55
 */
include "Extractor.php";
include "SobekApiHandler.php";

const FILES_DIRECTORY = "./grafo/files/";
const GRAPHS_DIRECTORY = "./grafo/grafos/";
const ERROR_EXTRACTION = Extractor::ERROR_EXTRACTION;
const ERROR_UNKNOWN_EXTENSION = Extractor::ERROR_UNKNOWN_EXTENSION;

function receivedUrl() {
    return isset($_POST["URL"]) && !is_null($_POST["URL"]);
}

function receivedTxt() {
    return isset($_FILES['TXT']) && !is_null($_FILES['TXT']);
}

function receivedPdf() {
    return isset($_FILES['PDF']) && !is_null($_FILES['PDF']);
}

function receivedDocx() {
    return isset($_FILES['DOCX']) && !is_null($_FILES['DOCX']);
}

function receivedDoc() {
    return isset($_FILES['DOC']) && !is_null($_FILES['DOC']);
}

function urlFromPost() {
    if(receivedUrl()){
        return $_POST['URL'];
    } else {
        return null;
    }
}

function parametersFromPost() {
    $parameters["language"] = isset($_POST['LANG']) ? trim($_POST['LANG']) : null;
    $parameters["avg_concepts"] = isset($_POST['AVGCON']) ? trim($_POST['AVGCON']) : null;
    $parameters["min_freq"] = isset($_POST['FREQ']) ? trim($_POST['FREQ']) : null;
    $parameters["thesaurus"] = isset($_POST['THES']) ? trim($_POST['THES']) : null;
    $parameters["verbs"] = isset($_POST['VERBS']) ? trim($_POST['VERBS']) : null;
    $parameters["nouns"] = isset($_POST['NOUNS']) ? trim($_POST['NOUNS']) : null;
    $parameters["adj"] = isset($_POST['ADJ']) ? trim($_POST['ADJ']) : null;
    $parameters["adv"] = isset($_POST['ADV']) ? trim($_POST['ADV']) : null;
    $parameters["inter"] = isset($_POST['INTER']) ? trim($_POST['INTER']) : null;
    $parameters["pron"] = isset($_POST['PRON']) ? trim($_POST['PRON']) : null;
    $parameters["todo"] = isset($_POST['TODO']) ? trim($_POST['TODO']) : null;
    $parameters["tobe"] = isset($_POST['TOBE']) ? trim($_POST['TOBE']) : null;
    $parameters["tohave"] = isset($_POST['TOHAVE']) ? trim($_POST['TOHAVE']) : null;

    return $parameters;
}

function mobileIdFromPost() {
    $mobileId = isset($_POST['MOB_ID']) ? trim($_POST['MOB_ID']) : null;

    return $mobileId;
}

function errorMovingFile($extension){
    switch ($_FILES[$extension]['error']){
        case 1 || 2 :
            return "BIG FILE";
        default:
            return "ERROR UPLOADING FILE";
    }
}

function saveXmlFile($xmlText, $mobileId){
    $xmlFileHandler = fopen(GRAPHS_DIRECTORY.$mobileId.".xml", "w");
    fwrite($xmlFileHandler, $xmlText);
    fclose($xmlFileHandler);
}

function textFromFile($extension, $mobileId){
    if(Extractor::unknownExtension($extension)){
        echo ERROR_UNKNOWN_EXTENSION;
        return null;
    }

    $tempFilename = $mobileId.".".$extension;

    if(! move_uploaded_file($_FILES[$extension]['tmp_name'], FILES_DIRECTORY.$tempFilename)){
        echo errorMovingFile($extension);
        return null;
    } else {
        $oldDir = getcwd();
        chdir(FILES_DIRECTORY);

        $extractedText = Extractor::textFromFile($extension, $tempFilename);

        if(strcmp($extractedText, ERROR_EXTRACTION) == 0){
            echo ERROR_EXTRACTION;
            chdir($oldDir);
            return null;
        } else {
            chdir($oldDir);
            return $extractedText;
        }
    }
}


$parameters = parametersFromPost();
$mobileId = mobileIdFromPost();

if(receivedUrl()){
    $url = urlFromPost();
    $extractedText = Extractor::textFromWebPage($url);
    $xmlText = SobekApiHandler::getGraphXml($extractedText, $parameters);
    if($xmlText !== FALSE) saveXmlFile($xmlText, $mobileId);

    return;
} else if(receivedTxt()) {
    $extension = "TXT";
} else if(receivedPdf()) {
    $extension = "PDF";
} else if(receivedDocx()) {
    $extension = "DOCX";
} else if(receivedDoc()) {
    $extension = "DOC";
} else {
    echo ERROR_UNKNOWN_EXTENSION;
    return;
}

$extractedText = textFromFile($extension, $mobileId);
$xmlText = SobekApiHandler::getGraphXml($extractedText, $parameters);
if($xmlText !== FALSE) saveXmlFile($xmlText, $mobileId);


