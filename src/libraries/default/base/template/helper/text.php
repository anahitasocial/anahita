<?php
/**
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateHelperText extends LibBaseTemplateHelperAbstract implements KServiceInstantiatable
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
     * Truncates a text.
     *
     * @param string $text    The text to truncate
     * @param array  $options Truncation options. Can be 'length'=>integer, 'read_more'=>boolean,
     *                        'ending'=>string, 'exact'=>boolean, 'consider_html'=>false
     *
     * @return string
     */
    public function truncate($text, $options = array())
    {
        $default = array('length' => 300, 'read_more' => false, 'ending' => '...', 'exact' => true, 'consider_html' => false);

        $options = array_merge($default, $options);

        extract($options, EXTR_SKIP);

        if ($consider_html) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';

            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                    // if tag is a closing tag (f.e. </b>)
                    } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                    // if tag is an opening tag (f.e. <b>)
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }

                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                --$left;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= KHelperString::substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }

                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = KHelperString::substr($text, 0, $length - strlen($ending));
            }
        }

        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = KHelperString::substr($truncate, 0, $spacepos);
            }
        }

        // add the defined ending to the text
        $truncate .= $ending;

        if ($consider_html) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</'.$tag.'>';
            }
        }

        if ($read_more) {
            $short_id = uniqid();
            $full_id = uniqid();
            $read_more = ' <a class="an-read-more" data-trigger="ReadMore" data-short="'.$short_id.'" data-full="'.$full_id.'" href="#">'.AnTranslator::_('LIB-AN-READMORE').'</a>';
            $read_less = ' <a class="an-read-less" data-trigger="ReadLess" data-short="'.$short_id.'" data-full="'.$full_id.'" href="#">'.AnTranslator::_('LIB-AN-READLESS').'</a>';
            $truncate = '<div id="'.$short_id.'">'.$truncate.$read_more.'</div>';
            $truncate .= '<div id="'.$full_id.'" class="hide">'.$text.$read_less.'</div>';
        }

        return $truncate;
    }

    /**
     * returns substring of characters around a searchword.
     *
     * @param string The source string
     * @param int Number of chars to return
     * @param string The searchword to select around
     *
     * @return string
     */
    public function substring($text, $searchword, $length = 200)
    {
        $textlen = KHelperString::strlen($text);
        $lsearchword = KHelperString::strtolower($searchword);
        $wordfound = false;
        $pos = 0;

        while ($wordfound === false && $pos < $textlen) {
            if (($wordpos = @KHelperString::strpos($text, ' ', $pos + $length)) !== false) {
                $chunk_size = $wordpos - $pos;
            } else {
                $chunk_size = $length;
            }

            $chunk = KHelperString::substr($text, $pos, $chunk_size);
            $wordfound = KHelperString::strpos(KHelperString::strtolower($chunk), $lsearchword);

            if ($wordfound === false) {
                $pos += $chunk_size + 1;
            }
        }//while

        if ($wordfound !== false) {
            return (($pos > 0) ? '...&nbsp;' : '').$chunk.'&nbsp;...';
        } else {
            if (($wordpos = @KHelperString::strpos($text, ' ', $length)) !== false) {
                return KHelperString::substr($text, 0, $wordpos).'&nbsp;...';
            } else {
                return KHelperString::substr($text, 0, $length);
            }
        }
    }

    /**
     * wraps the provided keywords in a text with span tags containing the highlight css tag.
     *
     *  @param string $text
     *  @param array  $words
     *
     *  @return string of processed text
     */
    public function highlight($text, $words, $min = 3)
    {
        $words = KConfig::unbox($words);
        settype($words, 'array');
        foreach ($words as $word) {
            if (strlen($word) >= $min) {
                $text = KHelperString::str_ireplace($word, '<span class="an-text-highlight">'.$word.'</span>', $text);
            }
        }

        return $text;
    }

    /**
     * Return a size in a human friendly way.
     *
     * @param int $size Return a size in a human friendly way
     *
     * @return string
     */
    public function size($size)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = 1099511627776;
        if (!$size) {
            return '0 B';
        } elseif ($size < $kb) {
            return $size.' B';
        } elseif ($size < $mb) {
            return round($size / $kb, 2).' KB';
        } elseif ($size < $gb) {
            return round($size / $mb, 2).' MB';
        } elseif ($size < $tb) {
            return round($size / $gb, 2).' GB';
        } else {
            return round($size / $tb, 2).' TB';
        }
    }

    /**
     * Return a sanitized version of a text which can be assigned to a javascript variable.
     *
     * @param string $text The text to sanitize
     *
     * @return string
     */
    public function script($text)
    {
        return htmlspecialchars(
               $this->getService('koowa:filter.string')
               ->sanitize(KHelperString::str_ireplace(array("\r\n", "\n"), '', $text)), ENT_QUOTES);
    }
}
