<?php

/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 14/02/2017
 * Time: 20:17
 */
class SobekApiHandler {

    const SOBEK_URL = "http://sobek.ufrgs.br/webservice/sobek.php";
    const ERROR_SOBEK_MINING = "FAILED SOBEK EXTRACTION";

    public static function getGraphXml($extractionText, $extractionParameters){

        $arguments = "";
        if(strcmp($extractionParameters["avg_concepts"], "") != 0){
            $arguments .= "-c ".$extractionParameters["avg_concepts"]." ";
        }
        if(strcmp($extractionParameters["min_freq"], "") != 0){
            $arguments .= "-m ".$extractionParameters["min_freq"]." ";
        }

        if(strcmp($extractionParameters["thesaurus"], "true") == 0) $arguments .= "-d ";
        $arguments .= "-l ".$extractionParameters["language"]." ";
        if(strcmp($extractionParameters["language"], "1") == 0){ //Language is portuguese
            if(strcmp($extractionParameters["verbs"], "false") == 0 || strcmp($extractionParameters["nouns"], "false") == 0 ||
                strcmp($extractionParameters["adj"], "false") == 0 || strcmp($extractionParameters["adv"], "false") == 0 ||
                strcmp($extractionParameters["inter"], "false") == 0){
                $arguments .= "-tree ";
                if(strcmp($extractionParameters["verbs"], "false") == 0){
                    $arguments .= "tagVebos ";
                }
                if(strcmp($extractionParameters["nouns"], "false") == 0){
                    $arguments .= "tagSubs ";
                }
                if(strcmp($extractionParameters["adj"], "false") == 0){
                    $arguments .= "tagAdje ";
                }
                if(strcmp($extractionParameters["adv"], "false") == 0){
                    $arguments .= "tagAdverbio ";
                }
                if(strcmp($extractionParameters["inter"], "false") == 0){
                    $arguments .= "tagInterjeicao ";
                }
            }
        }else{ //Language is english
            if(strcmp($extractionParameters["verbs"], "false") == 0 || strcmp($extractionParameters["nouns"], "false") == 0 ||
                strcmp($extractionParameters["adj"], "false") == 0 || strcmp($extractionParameters["adv"], "false") == 0 ||
                strcmp($extractionParameters["pron"], "false") == 0 || strcmp($extractionParameters["todo"], "false") == 0 ||
                strcmp($extractionParameters["tobe"], "false") == 0 || strcmp($extractionParameters["tohave"], "false") == 0){
                $arguments .= "-tree ";
                if(strcmp($extractionParameters["verbs"], "false") == 0){
                    $arguments .= "removeAllVerbs ";
                }
                if(strcmp($extractionParameters["nouns"], "false") == 0){
                    $arguments .= "removeNouns ";
                }
                if(strcmp($extractionParameters["adj"], "false") == 0){
                    $arguments .= "removeAdjectives ";
                }
                if(strcmp($extractionParameters["adv"], "false") == 0){
                    $arguments .= "removeAdverbs ";
                }
                if(strcmp($extractionParameters["pron"], "false") == 0){
                    $arguments .= "removePronouns ";
                }
                if(strcmp($extractionParameters["todo"], "false") == 0){
                    $arguments .= "removeVerbsDo ";
                }
                if(strcmp($extractionParameters["tobe"], "false") == 0){
                    $arguments .= "removeVerbsBe ";
                }
                if(strcmp($extractionParameters["tohave"], "false") == 0){
                    $arguments .= "removeVerbsHave ";
                }
            }
        }

        $data = array('entrada' => $arguments."-t ".$extractionText);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = @file_get_contents(SobekApiHandler::SOBEK_URL, false, $context);
        if($result === FALSE){
            echo SobekApiHandler::ERROR_SOBEK_MINING;
            return FALSE;
        }else{
            return $result;
        }
    }
}