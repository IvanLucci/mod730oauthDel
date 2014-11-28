<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DecoderController
 *
 * @author antonio.pastorino@gmail.com
 */
class Decoder_DecodeController extends Zend_Controller_Action {
    
    
    public function init() {
        
    }
    
    public function indexAction(){       
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $request = $this->getRequest();
        
        if(!$request->isPost()){
	    $this->getResponse()->setHttpResponseCode(400);
            echo 'you should make a POST request..';
            return;
        }
        
        if(!$token = $request->getParam('token')){
            $this->getResponse()->setHttpResponseCode(400);
            echo 'You should specify a token';
            return;
        }
        
        if(!$secret = $request->getParam('secret')){
	    $this->getResponse()->setHttpResponseCode(400);
            echo 'You should specify a secret in order to decode this token';
            return;
        }        
        
        $tt = explode(".", $token);

        if(count($tt) != 3){
            echo "There's a problem with the chunks, they should be 3, but they are: ".count($tt)."\n";
            return;
        }
        
//        echo "Fine, your chunked token is:\n";
        $c=0;        
        foreach($tt as $t){
//            echo "[".$c++."]\n".$t."\n";           
        }
        
//        echo "---------------------------\n";
        //first the header, json decoding it

        if( ! $ec_header = json_decode(base64_decode($tt[0]), true) ){
            echo "There's a problem with the header format\nABORT!";
            return;
        }       
        
//        echo "Your decoded header is:\n".base64_decode($tt[0])."\n";
//        echo "---------------------------\n";
        
        
        //then, from the header, retrieve the initialization vector(CBC)

        $iv = base64_decode($ec_header['iv']);        
        
        $ciphertext = base64_decode($tt[2]);
        
        $signed_token = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secret, $ciphertext, MCRYPT_MODE_CBC, $iv);
        //echo "Deciphering we have:\n".$signed_token."\n";
        echo "Plaintext token is:\n";
        echo $signed_token;
        
    }
    
    
}
