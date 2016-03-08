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
    public function __jsAction($device_name = '', $device_version = '', $platform = '')
    {
        $container = $this->container;

        $appId = $container->hasParameter('mrapps_onesignal.parameters.app_id') ? $container->getParameter('mrapps_onesignal.parameters.app_id') : '';
        $appName = $container->hasParameter('mrapps_onesignal.parameters.app_name') ? $container->getParameter('mrapps_onesignal.parameters.app_name') : '';
        $gcmSenderId = $container->hasParameter('mrapps_onesignal.web_push.gcm_sender_id') ? $container->getParameter('mrapps_onesignal.web_push.gcm_sender_id') : '';
        $safariWebId = $container->hasParameter('mrapps_onesignal.web_push.safari_web_id') ? $container->getParameter('mrapps_onesignal.web_push.safari_web_id') : '';


        return $this->render('MrappsOnesignalBundle:Onesignal:js.html.twig', array(
            "app_id" => $appId,
            "app_name" => $appName,
            "gcm_sender_id" => $gcmSenderId,
            "safari_web_id" => $safariWebId,
            "device_name" => trim($device_name),
            "device_version" => trim($device_version),
            "platform" => trim($platform),
        ));
    }

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
