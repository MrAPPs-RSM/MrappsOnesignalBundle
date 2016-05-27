<?php

namespace Mrapps\OnesignalBundle\Repository;

use Mrapps\OnesignalBundle\Model\UserInterface;
use Mrapps\OnesignalBundle\Classes\Utils;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserPlayerRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllPlayersByUsers($users = array())
    {

        $output = array();
        if (!is_array($users)) $users = array($users);

        if (count($users) > 0) {

            $em = $this->getEntityManager();

            $ups = $em->createQuery("
                SELECT up
                FROM MrappsOnesignalBundle:UserPlayer up
                WHERE up.user IN (?1)
            ")->setParameter('1', $users)->execute();

            foreach ($ups as $up) {
                $playerId = ($up->getPlayer() !== null) ? trim($up->getPlayer()->getPlayerId()) : '';
                if (strlen($playerId) > 0 && !in_array($playerId, $output)) {
                    $output[] = $playerId;
                }
            }
        }

        return $output;
    }

    public function unsetUserPlayers(UserInterface $user = null)
    {

        if ($user !== null) {

            $em = $this->getEntityManager();

            $players = array();
            $idsPlayers = array();

            //Elimina relazioni
            $ups = $em->getRepository('MrappsOnesignalBundle:UserPlayer')->findBy(array('user' => $user));
            foreach ($ups as $up) {
                $player = $up->getPlayer();
                if ($player !== null && !in_array($player->getId(), $idsPlayers)) {
                    $idsPlayers[] = $player->getId();
                    $players[] = $player;
                }

                $em->remove($up);
            }
            $em->flush();

            //Elimina i Player inattivi
            foreach ($players as $p) {
                if (Utils::deactivatePlayer($p->getPlayerId())) {
                    $em->getRepository('MrappsOnesignalBundle:Player')->deleteInactivePlayer($p, false);
                }
            }
            $em->flush();

            return true;
        }

        return false;
    }
}
