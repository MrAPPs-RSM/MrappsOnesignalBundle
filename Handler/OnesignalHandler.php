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

    /**
     * Invia una notifica.
     *
     * @param array $data array che contiene array/string message, array/string title,url,array parameters per specificare altri parametri
     * @param string $type segments/players
     * @param array $sendTo array di players o segmenti verso cui inviare la notifica
     *
     * @return array Ritorna se la chiamata è stata eseguita correttamente ed eventuali errori specificati nella chiamata
     *
     */
    public function sendNotification($data = array(), $type = null, $sendTo = array())
    {
        $result = array("success" => false, "message" => "Parametri non corretti", "error_code" => 1001);//parametri passati non corretti

        //Sistemazioni parametri
        if (!is_array($data)) $data = array();

        if (!isset($data['message']) || empty($data['message'])) {
            return $result;
        }

        if (!is_array($data['message'])) {
            $messages = array(
                'en' => $data['message'],
                'it' => $data['message'],
            );
        } else {
            foreach ($data['message'] as $mess) {
                if (empty($mess)) {
                    return $result;
                }
            }

            $messages = $data['message'];
        }

        $titles = null;
        if (isset($data['title'])) {
            if (!is_array($data['title'])) {
                $titles = array(
                    'en' => $data['title'],
                    'it' => $data['title'],
                );
            } else {
                $titles = $data['title'];
            }
        }

        $url = (isset($data['url'])) ? trim($data['url']) : '';


        $type = $this->getCorrectSendToType($type);

        if (count($messages) > 0 && $type !== null) {

            $params = $this->getParameters();

            //Parametri di base
            $fields = array(
                'app_id' => $params['app_id'],
                'contents' => $messages,
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
            if ($titles !== null &&
                count($titles) > 0
            ) {
                $fields['headings'] = $titles;
            }

            //URL da aprire al click sulla notifica
            if (strlen($url) > 0) {
                $fields['url'] = $url;
            }

            if (isset($data["parameters"])) {
                $fields = array_merge($fields, $data["parameters"]);
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


            if ($response === false) {
                $result["message"] = "Non è stato possibile inviare la notifica";
                $result["error_code"] = 1002;//errore curl

                return $result;
            }

            $responseArr = json_decode($response, true);

            if ($responseArr !== null) {

                if (isset($responseArr["errors"])) {

                    if (is_array($responseArr["errors"])) {
                        if (array_key_exists("invalid_player_ids", $responseArr["errors"])) {

                            foreach ($responseArr["errors"]["invalid_player_ids"] as $playerId) {
                                $this->em->getRepository("MrappsOnesignalBundle:Player")->deletePlayer($playerId, false);
                            }
                            $this->em->flush();
                        }else{
                            foreach ($responseArr["errors"] as $error) {
                                $result["message"] = $error;
                                $result["error_code"] = 1003;//All included players are not subscribed
                            }
                        }
                    }

                } else {
                    $result["success"] = true;
                    $result["message"] = null;
                    $result["error_code"] = null;
                }

                return $result;
            }

            $result["message"] = "Non è stato possibile inviare la notifica";
            $result["error_code"] = 1002; //la risposta non è json
            return $result;
        }

        return $result;
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

    public function sendNotificationToMultiplePlayers($data = array(), $players = array())
    {

        if (!is_array($players)) $players = array();

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