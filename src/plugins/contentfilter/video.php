<?php

/**
 * Video content filter.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgContentfilterVideo extends PlgContentfilterAbstract
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
        $this->_youtube($text);
        $this->_vimeo($text);

        return $text;
    }

    /**
     * Checks the text for a vimeo URL.
     *
     * @param string $text The text to filter
     *
     * @return string
     */
     protected function _vimeo(&$text)
     {
         $matches = array();

         if (preg_match_all('%http[s]?://\S*vimeo.com/(\d+)%', $text, $matches)) {
             foreach ($matches[1] as $index => $id) {
                 $video = sprintf('<div class="an-media-video" data-trigger="video-player" data-type="vimeo" data-video-id="%d"></div>', $id);
                 $text = str_replace($matches[0][$index], $video, $text);
             }
         }
     }

    /**
     * Checks the text for a youtube URL.
     *
     * @param string $text The text to filter
     *
     * @return string
     */
    protected function _youtube(&$text)
    {
        $matches = array();

        if (preg_match_all('%http[s]?://\S*(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $text, $matches)) {
            foreach ($matches[1] as $index => $id) {
                $video = sprintf('<div class="an-media-video" data-trigger="video-player" data-type="youtube" data-video-id="%s"></div>', $id);
                $text = str_replace($matches[0][$index], $video, $text);
            }
        }
    }
}
