<?php

namespace Mrapps\OnesignalBundle\Handler;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Mrapps\OnesignalBundle\Model\UserInterface;
use Mrapps\OnesignalBundle\Utils;


class OnesignalHandler
{
    private $container;
    private $em;
    private $allowedTypes = array(
        'segments',
        'players',
    );

    public function __construct(Container $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    private function getParameters()
    {

        $appId = ($this->container->hasParameter('mrapps_onesignal.parameters.app_id')) ? $this->container->getParameter('mrapps_onesignal.parameters.app_id') : '';
        $restKey = ($this->container->hasParameter('mrapps_onesignal.web_push.rest_api_key')) ? $this->container->getParameter('mrapps_onesignal.web_push.rest_api_key') : '';

        return array(
            'app_id' => $appId,
            'rest_api_key' => $restKey,
        );
    }

    private function getCorrectSendToType($type)
    {

        $type = strtolower(trim($type));
        if (!in_array($type, $this->allowedTypes)) {
            $type = null;
        }

        return $type;
    }

    public function sendNotification($data = array(), $type = null, $sendTo = array())
    {

        //Sistemazioni parametri
        if (!is_array($data)) $data = array();
        $message = (isset($data['message'])) ? trim($data['message']) : '';
        $title = (isset($data['title'])) ? trim($data['title']) : '';
        $url = (isset($data['url'])) ? trim($data['url']) : '';

        $type = $this->getCorrectSendToType($type);

        if (strlen($message) > 0 && $type !== null) {

            $params = $this->getParameters();

            //Messaggio
            $content = array(
                'en' => $message,
                'it' => $message,
            );

            //Parametri di base
            $fields = array(
                'app_id' => $params['app_id'],
                'contents' => $content,
                'isAnyWeb' => 1,
            );

            switch ($type) {
                case 'segments':
                    if (!is_array($sendTo)) $sendTo = array('All');
                    $fields['included_segments'] = $sendTo;
                    break;
                case 'players':
                    if (!is_array($sendTo)) $sendTo = array();
                    $fields['include_player_ids'] = $sendTo;
                    break;
            }


            //Titolo della notifica
            if (strlen($title) > 0) {
                $headings = array(
                    'en' => $title,
                    'it' => $title,
                );
                $fields['headings'] = $headings;
            }

            //URL da aprire al click sulla notifica
            if (strlen($url) > 0) {
                $fields['url'] = $url;
            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Authorization: Basic ' . $params['rest_api_key']));
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

    public function sendNotificationToUser($data = array(), UserInterface $user = null)
    {

        $players = $this->em->getRepository('MrappsOnesignalBundle:UserPlayer')->getAllPlayersByUsers($user);
        if (count($players) > 0) {
            return $this->sendNotification($data, 'players', $players);
        }

        return null;
    }

    public function sendNotificationToMultipleUsers($data = array(), $users = array())
    {

        if (!is_array($users)) $users = array();

        $players = $this->em->getRepository('MrappsOnesignalBundle:UserPlayer')->getAllPlayersByUsers($users);
        if (count($players) > 0) {
            return $this->sendNotification($data, 'players', $players);
        }

        return null;
    }

    public function addPlayer(UserInterface $user = null, $playerID = '', $extraData = array())
    {
        return $this->em->getRepository("MrappsOnesignalBundle:Player")->addPlayer($user, $playerID, $extraData);
    }

    public function deactivatePlayer($playerID = '', $deletePlayer = true)
    {
        $success = Utils::deactivatePlayer($playerID);

        if ($success && $deletePlayer) {
            $this->em->getRepository('MrappsOnesignalBundle:Player')->deletePlayer($playerID);
        }

        return $success;
    }

    public function deactivateAllPlayersForUser(UserInterface $user = null)
    {

        if ($user !== null) {
            return $this->em->getRepository('MrappsOnesignalBundle:UserPlayer')->unsetUserPlayers($user);
        }

        return false;
    }


}