<?php

class Easqy_Shortcodes_Latest_Results
{
    public function __construct(Easqy $easqy) {

        $this->load_dependencies();
        $this->define_admin_hooks($easqy->get_loader());
        $this->define_public_hooks($easqy->get_loader());
    }


    private function load_dependencies() {
    }

    private function define_admin_hooks(Easqy_Loader $loader) {
    }

    private function define_public_hooks(Easqy_Loader $loader)
    {
        $loader->add_shortcode('easqy_latest_results', $this, 'latest_results');
        $loader->add_action( 'wp_ajax_easqy_sc_latest_results', $this, 'ajax_easqy_sc_latest_results' );
        $loader->add_action( 'wp_ajax_nopriv_easqy_sc_latest_results', $this, 'ajax_easqy_sc_latest_results' );

    }

    public function latest_results()
    {
        ob_start();
        ?>
        <div class="easqy-shortcode-latest-results">
            Chargement ...
        </div>
        <?php

        $script_asset_path = 'js/index.asset.php';
        $index_js= 'js/index.js';
        $styles  = 'js/index.css';
        $script_asset = require( $script_asset_path );

        wp_enqueue_script(
            'easqy-shortcode-latest-results',
            plugins_url( $index_js, __FILE__ ),
            $script_asset['dependencies'],
            time(), true );

        wp_enqueue_style(
            'easqy-shortcode-latest-results',
            plugins_url( $styles, __FILE__ )
        );

        return ob_get_clean();
    }

    // AJAX -----------------------------------------------------------------------

    private static function extract_latest_results($year, $maxCount) {

        $maxCount = max(1, intval($maxCount));
        $url='https://bases.athle.fr/asp.net/liste.aspx?frmpostback=true&frmbase=resultats&frmmode=1&frmespace=0&frmsaison='
            .$year
            .'2020&frmclub=078140&frmnom=&frmprenom=&frmsexe=&frmlicence=&frmdepartement=&frmligue=&frmcomprch=';

        $response = wp_remote_get( $url );
        if ( is_wp_error( $response ) )
            return false;

        $body = wp_remote_retrieve_body($response);

        // parse td tags
        $re = '/<td\s*class="datas\d">(.*?)<\/td>/m';
        preg_match_all($re, $body, $m, PREG_SET_ORDER, 0);
        if ( !is_array($m) )
            return false;

        $lineCount = min($maxCount, intdiv( count($m), $maxCount) );

        $result = array();
        for ($i=0; $i<$lineCount; ++$i) {

            $line= array();
            $line['date'] =  trim($m[10*$i + 0][1]);
            $athlete = trim($m[10*$i + 1][1]);
            $line['athlete']= null;
            if ($athlete != '&nbsp;') {
                preg_match_all('/"\s*>(.*?)<\/a>/m', $athlete, $a, PREG_SET_ORDER, 0);
                preg_match_all('/,\s*(.*)\s*,/m', $athlete, $id, PREG_SET_ORDER, 0);
                if (is_array($a) && is_array($id)) {
                    if ( (count($a) == 1) && (count($id) == 1) )
                        if ( (count($a[0]) == 2) && (count($id[0]) == 2) )
                            $line['athlete']= array(
                                'name' => trim($a[0][1]), 'id' => trim($id[0][1]) );
                }
            }
            $line['epreuve']=trim($m[10*$i + 2][1]);
            $line['tour']=null;

            $tour= trim($m[10*$i + 3][1]);
            if ($tour != '&nbsp;') {
                $line['tour'] = $tour;
            }

            $line['place']= trim($m[10*$i + 4][1]);

            $perf= str_replace('<b>', '', $m[10*$i + 5][1]);
            $perf= str_replace('</b>', '', $perf);
            $line['perf']= trim($perf);

            $pts= trim($m[10*$i + 6][1]);
            if ($pts == '&nbsp;')
                $line['pts']= null;
            else
                $line['pts']= $pts;

            $line['town']= trim($m[10*$i + 9][1]);

            $result []= $line;
        }
        return $result;
    }

    public function ajax_easqy_sc_latest_results() {

        $currentYear = intval(date("Y"));
        $result = self::extract_latest_results($currentYear, 10);
        if (!$result)
        {
            wp_send_json_error( );
            return;
        }

        if (count($result) < 10)
        {
            $lastYearResults = self::extract_latest_results($currentYear - 1, 10 - count($result));
            if ($lastYearResults) {
                $result = array_merge( $result, $lastYearResults);
            }

        }

        wp_send_json_success( $result );
    }

}
