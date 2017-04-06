<?php

 /**
  * Creates a preview from links to mediums.
  *
  * @category   Anahita
  *
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class PlgContentfilterMedium extends PlgContentfilterAbstract
 {
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
            'priority' => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Filter a value.
     *
     * @param string The text to filter
     *
     * @return string
     */
    public function filter($text)
    {
        preg_match_all('/(articles|topics|photos|todos|notes)\/([0-9]+)[-]?/', $text, $matches);

        $ids = $matches[2];

        foreach ($ids as $id) {
            $id = (int) $id;

            $medium = KService::get('repos:medium.medium')->getQuery()->disableChain()->id($id)->fetch();

            if (isset($medium->id) && $medium->authorize('access')) {
                if ($medium->getRepository()->hasBehavior('portraitable')) {
                    $caption = htmlspecialchars($medium->title, ENT_QUOTES);
                    $pattern = '/((?<!=\")[http]+[s]?:\/\/[^<>\s]+)\/photos\/'.$medium->id.'[-\w\-]*/';
                    $text = preg_replace($pattern,
                    '<div class="an-media-photo"><a data-trigger="MediaViewer" href="'.$medium->getPortraitURL('original').'" title="'.$caption.'" >'
                    .'<img alt="'.$caption.'" src="'.$medium->getPortraitURL('medium').'" />'
                    .'</a></div>',
                    $text);
                } else {
                    $pattern = '/((?<!=\")[http]+[s]?:\/\/[^<>\s]+)\/articles\/'.$medium->id.'[-\w\-]*/';

                    $template = '<div class="medium-preview">';

                    if ($medium->title) {
                        $template .= '<h4><a href="'.route($medium->getURL()).'">'.$medium->title.'</a></h4>';
                    }

                    if ($medium->excerpt) {
                        $template .= '<p>'.$medium->excerpt.'</p>';
                    }

                    $template .= '</div>';

                    $text = preg_replace($pattern, $template, $text);
                }
            }
        }

        return $text;
    }
 }
