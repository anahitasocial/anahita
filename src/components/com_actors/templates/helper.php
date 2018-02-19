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
 * Actor Helper.
 *
 * Provides methods to for rendering avatar/name for an actor
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsTemplateHelper extends LibBaseTemplateHelperAbstract implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);

            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Return an avatar URL of an actor. If actor doesn't have an avatar, then it reurns the
     * default avatar.
     *
     * @param ComActorsDomainEntityActor $actor
     * @param string                     $size
     *
     * @return string
     */
    public function getAvatarURL($actor, $size = 'square')
    {
        if ($actor->portraitSet()) {
            return $actor->getPortraitURL($size);
        }

        return $this->getService('com:base.template.asset')
                    ->getURL('lib_anahita/images/avatar/'.$size.'_default.gif');
    }

    /**
     * Draws an actor avatar image.
     *
     * @param ComActorsDomainEntityActor $actor
     * @param string                     $size
     * @param bool                       $linked if true it returns a linked image tag of not just an image tag
     * @param array                      $attr   link attributes
     *
     * @return string
     */
    public function avatar($actor, $size = 'square', $linked = true, $attr = array())
    {
        if (is_numeric($size)) {
            $width = "width=\"$size\"";
            $size = 'square';
        } else {
            $width = '';
        }

        $defaultAvatar = $this->getService('com:base.template.asset')
                              ->getURL('lib_anahita/images/avatar/'.$size.'_default.gif');

        if (is_null($actor) || !isset($actor->id)) {
            return '<img '.$width.' src="'.$defaultAvatar.'" id="actor-avatar-'.$size.'" size="'.$size.'" class="actor-avatar '.$size.'" />';
        }

        if ($actor->portraitSet()) {
            $src = $actor->getPortraitURL($size);

            $name = KHelperString::ucwords($actor->name);
            $verified = ($actor->verified) ? 'verified' : '';
            $img = '<img '.$width.' alt="'.$name.'" actorid="'.$actor->id.'" src="'.$src.'" id="actor-avatar-'.$actor->id.'" size="'.$size.'" class="actor-avatar actor-avatar-'.$actor->id.' '.$size.' '.$verified.'" />';
        } else {
            $img = '<img '.$width.' src="'.$defaultAvatar.'" id="actor-avatar-'.$size.'-'.$actor->id.'" size="'.$size.'" class="actor-avatar '.$size.'" />';
        }

        if ($linked && $actor->authorize('access')) {
            $url = route($actor->getURL());
            $verified = ($actor->verified) ? 'verified' : '';
            $img = '<a class="actor-avatar-link '.$verified.'" '.$this->_buildAttribute($attr).' actorid="'.$actor->id.'" href="'.$url.'" >'.$img.'</a>';
        }

        return $img;
    }

    /**
     * Draws an actor cover image.
     *
     * @param ComActorsDomainEntityActor $actor
     * @param string                     $size
     * @param bool                       $linked if true it returns a linked image tag of not just an image tag
     * @param array                      $attr   link attributes
     *
     * @return string
     */
    public function cover($actor, $size = 'large', $linked = true, $attr = array())
    {
        if (is_numeric($size)) {
            $width = "width=\"$size\"";
            $size = 'large';
        } else {
            $width = '';
        }

        if ($actor->coverSet()) {
            $src = $actor->getCoverURL($size);

            $name = KHelperString::ucwords($actor->name);
            $img = '<img '.$width.' alt="'.$name.'" class="cover" src="'.$src.'"  />';
        }

        if ($linked && $actor->authorize('access')) {
            $url = route($actor->getURL());
            $img = '<a '.$this->_buildAttribute($attr).' data-actor="'.$actor->id.'" href="'.$url.'" >'.$img.'</a>';
        }

        return $img;
    }

    /**
     * Draws an actor name.
     *
     * @param ComActorsDomainEntityActor $actor
     * @param bool                       $linked if TRUE return a linked name otherwise return name
     * @param array                      $attr   link attributes
     *
     * @return string
     */
    public function name($actor, $linked = true, $attr = array())
    {
        if (is_null($actor) || !isset($actor->id)) {
            $linked = false;
            $name = '<span class="actor-name">'.AnTranslator::_('LIB-AN-UNKOWN-PERSON').'</span>';
        } else {
            $name = '<span class="actor-name" actorid="'.$actor->id.'">'.$actor->name.'</span>';
            if($actor->verified){
              $name = $name.' <span class="icon icon-ok-sign"></span>';
            }
        }

        if (!$linked || !$actor->authorize('access')) {
            return (string) $name;
        }

        $url = route($actor->getURL());

        if (is_person($actor)) {
            $attr['title'] = '@'.$actor->alias;
        }

        $name = '<a class="actor-name" '.$this->_buildAttribute($attr).' actorid="'.$actor->id.'" href="'.$url.'" >'.$name.'</a>';

        return $name;
    }

    /**
     * Return pronoune, possessive noune or object noune for an actor. The type of
     * noune is passed as an option with key 'type'. If 'useyou' is passed it treat
     * the actor as second person.
     *
     * @param ComActorsDomainEntityActor $actor
     * @param array                      $options Options to pass
     *
     * @return string
     */
    public function noune($actor, $options = array())
    {
        $options = array_merge(array('useyou' => false), $options);
        $gender = strtolower($actor->gender);
        $type = $options['type'];

        if (!isset($actor) || is_null($actor)) {
            return '';
        }

        switch (strtolower($type)) {
            case 'pronoune'   :
                    if ($options['useyou']) {
                        $value = 'LIB-AN-YOU';
                    } else {
                        $value = $gender == 'male'
                                 ? 'LIB-AN-HE' : ($gender == 'female'
                                 ? 'LIB-AN-SHE'
                                 : 'LIB-AN-THEY');
                    }
                    break;

            case 'possessive' :

                    if ($options['useyou']) {
                        $value = 'LIB-AN-YOUR';
                        break;
                    }

                    $value = $gender == 'male'
                             ? 'LIB-AN-HIS' : ($gender == 'female'
                             ? 'LIB-AN-HER'
                             : 'LIB-AN-THEIR');
                    break;

            case 'objective' :
                    if ($options['useyou']) {
                        $value = 'LIB-AN-YOU';
                        break;
                    }

                    $value = $gender == 'male'
                             ? 'LIB-AN-HIM' : ($gender == 'female'
                             ? 'LIB-AN-HER'
                             : 'LIB-AN-THEM');
                    break;
        }

        return $value;
    }

    /**
     * Build html attribute.
     *
     * @return string
     */
    protected function _buildAttribute($attr)
    {
        $string = array();

        foreach ($attr as $key => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            $string[] = $key.'="'.$value.'"';
        }

        return implode(' ', $string);
    }
}
