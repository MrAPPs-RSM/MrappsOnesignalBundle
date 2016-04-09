<?php

namespace Mrapps\OnesignalBundle\Classes;

class Utils
{
    public static function deactivatePlayer($playerID = '')
    {
        if (strlen($playerID) > 0) {

            //Parametri di base
            $fields = array(
                'notification_types' => -2,  //-2 = unsubscribed
            );

            $url = "https://onesignal.com/api/v1/players/" . $playerID;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                return false;
            }

            $result = json_decode($response, true);

            if ($result !== null) {

                return true;
            }
        }

        return false;
    }
}