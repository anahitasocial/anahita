<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Privatable Behavior.
 *
 * Provides privacy for nodes
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorPrivatable extends AnDomainBehaviorAbstract
{
    /**
     * Graph Privacy Constants.
     */
    const GUEST = 'public';
    const REG = 'registered';
    const FOLLOWER = 'followers';
    const LEADER = 'leaders';
    const MUTUAL = 'mutuals';
    const ADMIN = 'admins';

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'attributes' => array(
                'access' => array('default' => self::GUEST),
                'permissions' => array(
                    'type' => 'json',
                    'default' => 'json',
                    'write' => 'private'
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set the access value of the node. Access value is basically a read persmission for a node.
     * This value can be checked during fetching the record from the database to see if the
     * viewer has access to it or not.
     *
     * @param int|string $access Access value. Can be a string or an id of another node
     *
     * @return LibBaseDomainBehaviorPrivatable
     */
    public function setAccess($value)
    {
        $values = (array) $value;

        foreach ($values as $key => $value) {
            if (empty($value)) {
                $value = self::GUEST;
            } elseif (!is_numeric($value)) {
                if (!in_array($value, array('public', 'registered', 'followers', 'special', 'leaders', 'mutuals', 'admins'))) {
                    $value = self::GUEST;
                }
            }

            if ($value == self::GUEST) {
                $values = array(self::GUEST);
                break;
            }

            if ($this->_mixer->authorize('setprivacyvalue', array('value' => $key)) === false) {
                unset($values[$key]);
            } else {
                $values[$key] = $value;
            }
        }

        asort($values);

        $values = array_unique($values);

        $this->set('access', implode(',', $values));

        return $this;
    }

    /**
     * Return a permission value for of a resource. If the permission doesn't exists then it returns the default
     * value.
     *
     * @param string $key     The name of the resource
     * @param mixed  $default The default value, if the permission is not set yet
     *
     * @return mixed
     */
    public function getPermission($key, $default = self::GUEST)
    {
        if ($key == 'access') {
            return $this->access;
        }

        return $this->permissions->get($key, $default);
    }

    /**
     * Sets a permission for a resource.
     *
     * @param string     $key   The name of the resource
     * @param int|string $value The value of the permission. Can be string or integer
     *
     * @return LibBaseDomainBehaviorPrivatable
     */
    public function setPermission($key, $value)
    {
        if (empty($value)) {
            $value = self::GUEST;
        }

        if ($key == 'access') {
            $this->access = $value;
        } else {
            $permission = clone $this->permissions;
            $permission[$key] = $value;
            $this->set('permissions', $permission);
        }

        return $this;
    }

    /**
     * Checks if an entity has a permission value for a key.
     *
     * @param string $key   The key permission key
     * @param string $value The value to to check if permission has
     *
     * @return bool
     */
    public function hasPermission($key, $value)
    {
        $values = explode(',', $this->getPermission($key));

        return in_array($value, $values);
    }

    /**
     * Return whether $actor has privellege perform $action on the entity.
     *
     * @param ComPeopleDomainEntityPerson $actor   The person who's trying to perform an operation on the entity
     * @param string                      $action  The name of the action being performed
     * @param string                      $default The default value to use if the there are no values are set for the operation
     *
     * @return bool
     */
    public function allows($actor, $action, $default = self::REG)
    {
        //keep a reference to mixer just in case
        $mixer = $this->_mixer;

        $owner = null;

        if (is($this->_mixer, 'ComActorsDomainEntityActor')) {
            $owner = $this->_mixer;
        } elseif ($this->isOwnable()) {
            $owner = $this->owner;
        }

        if (! empty($owner)) {
            //if actor and owner the same then
            //allow
            if ($actor->id == $owner->id) {
                return true;
            }

            //no operation is allowed if the owner is blokcing the $actor
            if ($owner->isFollowable() && $owner->blocking($actor)) {
                return false;
            }

            //no opreation is allowed if actor is not published and the accessor
            //is not admin
            if ($owner->isEnableable() && $owner->enabled == false) {
                if ($actor->isAdministrator() && !$actor->administrator($owner)) {
                    return false;
                }
            }
        }

        //an array of entities whose permission must return true
        $entities = array();

        if (!empty($owner)) {
            $entities[] = $owner;
        }

        if (!in_array($mixer, $entities)) {
            $entities[] = $mixer;
        }

        foreach ($entities as $entity) {
            $permissions = explode(',', $entity->getPermission($action, $default));
            $result = $this->checkPermissions($actor, $permissions, $owner);
            if ($result === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks an array of permissions against an accessor using the socialgraph between the
     * accessor and actor.
     *
     * @param ComActorsDomainEntityActor $actor       The actor who's trying to perform an operation
     * @param array                      $permissions An array of permissions
     * @param ComActorsDomainEntityActor $owner       If one of the permision
     *
     * @return bool
     */
    public function checkPermissions($actor, $permissions, $owner)
    {
        $result = true;

        //all must be false in order to return false
        foreach ($permissions as $value) {
            $value = pick($value, self::GUEST);

            switch ($value) {
                //public
                case self::GUEST :
                    $result = true;
                    break;
                //registered
                case self::REG :
                    $result = !$actor->guest();
                    break;
                //follower
                case self::FOLLOWER :
                    $result = $actor->following($owner) || $actor->leading($owner) || $actor->administrator($owner);
                    break;
                //leader
                case self::LEADER :
                    $result = $actor->leading($owner) || $actor->administrator($owner);
                    break;
                //mutual
                case self::MUTUAL :
                    $result = $actor->mutuallyLeading($owner) || $actor->administrator($owner);
                    break;
                case self::ADMIN :
                    $result = $actor->administrator($owner);
                    break;
                default :
                     $result = $actor->id == $value;
            }

            if ($result === true) {
                break;
            }
        }

        return $result;
    }

    /**
     * Creates a where statement that checks actor id and access column values against viewer socialgraph and viewer id.
     *
     * @param string  $actor_id The name of the columm containing actor ids.
     * @param KConfig $config   Configuration parameter
     * @param string  $access   The name of the column containing access values
     *
     * @return string
     */
    public function buildCondition($actor_id, $config, $access = '@col(access)')
    {
        $store = $this->_repository->getStore();
        $viewer = $config->viewer;
        $where[] = 'CASE';
        $where[] = "WHEN $access IS NULL THEN 1";

        //for testing purpose
        //if($config->visible_to_leaders && $viewer->id && count($viewer->blockedIds) > 0)
        //    $where[] = "WHEN FIND_IN_SET(@col(id), '".$store->quoteValue($viewer->blockedIds->toArray())."') THEN 1";

        $where[] = "WHEN FIND_IN_SET('".self::GUEST."', $access) THEN 1";

        if ($viewer->id) {
            $where[] = "WHEN FIND_IN_SET('".self::REG."', $access) THEN 1";

            $where[] = 'WHEN FIND_IN_SET('.$viewer->id.", $access) THEN 1";

            if ($config->graph_check) {
                $leader_ids = $store->quoteValue($viewer->leaderIds->toArray());
                $follower_ids = $store->quoteValue($viewer->followerIds->toArray());
                $mutual_ids = $store->quoteValue($viewer->mutualIds->toArray());
                $admin_ids = $store->quoteValue($viewer->administratingIds->toArray());
                $is_viewer = "$actor_id = {$viewer->id}";

                $viewer_is_follower = "$is_viewer  OR $actor_id IN (".$leader_ids.')';
                $viewer_is_leader = "$is_viewer  OR $actor_id IN (".$follower_ids.')';
                $viewer_is_mutual = "$is_viewer  OR $actor_id IN (".$mutual_ids.')';
                $viewer_is_admin = "$is_viewer  OR $actor_id IN (".$admin_ids.')';
                $requestable = $this->_repository->isFollowable() ? '@col(allowFollowRequest) = 1' : 'FALSE';

                //if privacy set to follow, show the actor only if viewer is a follower or viewer is a leader or actor
                //accecpts follow request
                $where[] = "WHEN FIND_IN_SET('".self::FOLLOWER."',$access) THEN $viewer_is_follower OR $viewer_is_leader OR $requestable";
                $where[] = "WHEN FIND_IN_SET('".self::LEADER."',$access) THEN $viewer_is_leader";

                if ($config->visible_to_leaders) {
                    $where[] = "WHEN FIND_IN_SET('".self::MUTUAL."',$access) THEN $viewer_is_leader";
                    $where[] = "WHEN FIND_IN_SET('".self::ADMIN."',$access) THEN $viewer_is_admin OR $viewer_is_leader";
                } else {
                    $where[] = "WHEN FIND_IN_SET('".self::MUTUAL."',$access) THEN $viewer_is_mutual";
                    $where[] = "WHEN FIND_IN_SET('".self::ADMIN."',$access) THEN $viewer_is_admin";
                }
            }
        }

        $where[] = 'ELSE 0';
        $where[] = 'END';

        return implode(' ', $where);
    }
}
