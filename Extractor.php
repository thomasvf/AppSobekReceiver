<?php

/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 26/01/2017
 * Time: 22:33
 */
include "UrlSolver.php";
class Extractor {
    const ERROR_UNKNOWN_EXTENSION = "UNKNOWN EXTENSION";
    const ERROR_EXTRACTION = "FAILED EXTRACTING TEXT";
    const CONTENT_START = "__CONTENT__START__";
    const CONTENT_END = "__CONTENT__END__";

    public static function unknownExtension($extension){
        return strcmp($extension, "TXT") && strcmp($extension, "PDF") && strcmp($extension, "DOCX")
            && strcmp($extension, "DOC");
    }


    private static function plainTextPhantom($address){
        $command = "phantomjs webTextExtractor.js ".$address;
        $response = htmlspecialchars(shell_exec($command));
        if(strcmp(trim($response), "fail") == 0){
            return $response;
        }

        $start = strpos($response, Extractor::CONTENT_START) + strlen(Extractor::CONTENT_START);
        $length = strpos($response, Extractor::CONTENT_END) - $start;
        $response = substr($response, $start, $length);
        return $response;
    }

    public static function textFromWebPage($address){
        $response = Extractor::plainTextPhantom($address);
        if(strcmp(trim($response), "fail") == 0){
            $urlSolver = new UrlSolver($address);
            $address = $urlSolver->getRepairedUrl();

            // Try again with repaired url.
            $response = Extractor::plainTextPhantom($address);
            if(strcmp(trim($response), "fail") == 0){
                return null;
            } else {
                return $response;
            }
        } else {
            return $response;
        }
    }

    public static function textFromFile($extension, $filename){
        if(Extractor::unknownExtension($extension)){
            return Extractor::ERROR_UNKNOWN_EXTENSION;
        }

        if(strcmp($extension, "TXT") == 0){
            $fileHandler = fopen($filename, "r");
            $extractedText = utf8_encode(fread($fileHandler, filesize($filename)));
            fclose($fileHandler);
        } else{
            $command = "java -jar TextExtractor.jar ".strtoupper($extension)." ".$filename;
            $extractedText = shell_exec($command);
        }

        return $extractedText;
    }
}