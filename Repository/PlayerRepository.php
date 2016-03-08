<?php

namespace Mrapps\OnesignalBundle\Repository;

use Mrapps\OnesignalBundle\Model\UserInterface;
use Mrapps\OnesignalBundle\Entity\Player;
use Mrapps\OnesignalBundle\Entity\UserPlayer;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends \Doctrine\ORM\EntityRepository
{
    public function addPlayer(UserInterface $user = null, $playerID = '', $extraData = array()) {
        
        $output = array(
            'player' => null,
            'user_player' => null,
        );

        if($user !== null && strlen($playerID) > 0) {

            $em = $this->getEntityManager();

            //Informazioni aggiuntive
            if(!is_array($extraData)) $extraData = array();
            $deviceName = (isset($extraData['device_name'])) ? trim($extraData['device_name']) : null;
            $deviceVersion = (isset($extraData['device_version'])) ? trim($extraData['device_version']) : null;
            $platform = (isset($extraData['platform'])) ? trim($extraData['platform']) : null;
            $registrationID = (isset($extraData['registration_id'])) ? trim($extraData['registration_id']) : null;

            //Il Player esiste già?
            $player = $this->findOneBy(array('playerId' => $playerID));

            //Creazione nuovo Player
            if($player == null) {
                $player = new Player();
                $player->setPlayerId($playerID);
            }

            //Modifica dati aggiuntivi Player
            $player->setDeviceName($deviceName);
            $player->setDeviceVersion($deviceVersion);
            $player->setPlatform($platform);
            $player->setRegistrationId($registrationID);

            $em->persist($player);
            $em->flush($player);

            //Associazione UserInterface-Player
            $up = $em->getRepository('MrappsOnesignalBundle:UserPlayer')->findOneBy(array('user' => $user, 'player' => $player));
            if($up == null) {

                $up = new UserPlayer();
                $up->setUser($user);
                $up->setPlayer($player);

                $em->persist($up);
                $em->flush($up);
            }

            $output['player'] = $player;
            $output['user_player'] = $up;
        }
        
        return $output;
    }
    
    public function deleteInactivePlayer(Player $player = null) {
        
        if($player !== null) {
            
            $em = $this->getEntityManager();
            
            //Elimina il player solo se non c'è nessun utente associato
            $ups = $em->createQuery("
                SELECT COUNT(up)
                FROM MrappsOnesignalBundle:UserPlayer up
                WHERE up.player = :player
            ")->setParameters(array('player' => $player))->getSingleScalarResult();
            if(count($ups) == 0) {
                $em->remove($player);
                $em->flush();
                
                return true;
            }
        }
        
        return false;
    }
}
