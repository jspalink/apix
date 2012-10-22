<?php
namespace Apix\Output;

use Apix\Output\Adapter;

class Html extends Adapter
{

    /**
     * Holds the media type for the output.
     * @var string
     * @see http://www.ietf.org/rfc/rfc2854.txt
     */
    public $contentType = 'text/html';

    /**
     * {@inheritdoc}
     */
    public function encode(array $data, $rootNode='root')
    {
        return $this->validate(
            $this->_recursivelyAppend(
                array($rootNode => $data)
            )
        );
    }

    protected function _recursivelyAppend(array $results)
    {
        $out = '<ul>';
        foreach ($results as $k => $v) {
            $out .= "<li>$k: ";
            $out .= is_array($v) ? $this->_recursivelyAppend($v, $k) : $v;
            $out .= '</li>';
        }
        $out .= '</ul>';

        return $out;
    }

    protected function validate($html)
    {
        if (extension_loaded('tidy')) {
            $tidy = new \tidy();
            $conf = array(
                // PHP Bug: commenting out 'indent' (with true or false)
                // for some weird reason does chnage the Transfer-Encoding!
                'indent'			=> true,
                'tidy-mark'			=> false,
                'clean'				=> true,
                'output-xhtml'		=> false,
                'show-body-only'	=> true,
            );
            $tidy->parseString($html, $conf, 'UTF8');
            $tidy->cleanRepair();

            $html = $tidy->value; // with DOCTYPE
            #return $tidy->html()->value;
            #return tidy_get_output($tidy);
        }

        return $html;
    }

}
