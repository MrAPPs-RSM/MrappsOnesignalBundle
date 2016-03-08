<?php

namespace Mrapps\OnesignalBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/mrapps/onesignal")
 */
class OnesignalController extends Controller
{
    /**
     * @Route("/reg_player", name="mrapps_onesignal_regplayer")
     */
    public function regplayerAction(Request $request)
    {
        //Esempio: userId=X&registrationId=Y
        $content = $request->getContent();
        $vars = array();
        parse_str($content, $vars);

        $playerID = (isset($vars['userId'])) ? trim($vars['userId']) : '';
        $registrationID = (isset($vars['registrationId'])) ? trim($vars['registrationId']) : '';
        $deviceName = (isset($vars['device_name'])) ? trim($vars['device_name']) : '';
        $deviceVersion = (isset($vars['device_version'])) ? trim($vars['device_version']) : '';
        $platform = (isset($vars['platform'])) ? trim($vars['platform']) : '';

        try {
            //Utente loggato
            $user = $this->getUser();
            if(!is_object($user)) $user = null;
        } catch (\Exception $ex) {
            $user = null;
        }

        //Registrazione Player
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('MrappsOnesignalBundle:Player')->addPlayer($user, $playerID, array(
            'device_name' => $deviceName,
            'device_version' => $deviceVersion,
            'platform' => $platform,
            'registration_id' => $registrationID,
        ));

        return new JsonResponse(array('success' => true));
    }
}
