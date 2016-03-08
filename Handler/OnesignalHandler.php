<?php

namespace Mrapps\OnesignalBundle\Handler;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;


class OnesignalHandler
{
    private $container;
    private $em;
    
    public function __construct(Container $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    private function getParameters() {

        $appId = ($this->container->hasParameter('mrapps_onesignal.parameters.app_id')) ? $this->container->getParameter('mrapps_onesignal.parameters.app_id') : '';
        $restKey = ($this->container->hasParameter('mrapps_onesignal.parameters.rest_api_key')) ? $this->container->getParameter('mrapps_onesignal.parameters.rest_api_key') : '';

        return array(
            'app_id' => $appId,
            'rest_api_key' => $restKey,
        );
    }

    public function sendNotification($data = array(), $segments = null) {

        //Sistemazioni parametri
        if(!is_array($data)) $data = array();
        $message = (isset($data['message'])) ? trim($data['message']) : '';
        $title = (isset($data['title'])) ? trim($data['title']) : '';
        $url = (isset($data['url'])) ? trim($data['url']) : '';

        if(!is_array($segments)) $segments = array('All');

        if(strlen($message) > 0) {

            $params = $this->getParameters();

            //Messaggio
            $content = array(
                'en' => $message,
                'it' => $message,
            );

            //Parametri di base
            $fields = array(
                'app_id' => $params['app_id'],
                'included_segments' => $segments,
                'contents' => $content,
                'isAnyWeb' => 1,
            );

            //Titolo della notifica
            if(strlen($title) > 0) {
                $headings = array(
                    'en' => $title,
                    'it' => $title,
                );
                $fields['headings'] = $headings;
            }

            //URL da aprire al click sulla notifica
            if(strlen($url) > 0) {
                $fields['url'] = $url;
            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Authorization: Basic '.$params['rest_api_key']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }

        return null;
    }

    public function deactivatePlayer($playerID = '') {

        if(strlen($playerID) > 0) {

            //Parametri di base
            $fields = array(
                'notification_types' => -2,  //-2 = unsubscribed
            );

            $url = "https://onesignal.com/api/v1/players/".$playerID;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }

        return null;
    }


}