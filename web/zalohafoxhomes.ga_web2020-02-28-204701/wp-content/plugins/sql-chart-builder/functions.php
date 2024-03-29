<?php
if (!defined('ABSPATH')) {
    die;
}
//postinstall function
function guaven_sqlcharts_load_defaults()
{
    if (get_option("guaven_sqlcharts_already_installed_2") === false) {
        update_option("guaven_sqlcharts_already_installed_2", "1");
        guaven_sqlcharts_install_first_data();

    }
}




function guaven_sqlcharts_my_admin_notice()
{
    global $post;
    if (!empty($post) and $post->post_type == 'gvn_schart'):
        if (!current_user_can('manage_options')) {
            echo '<br><br>
  <div class="updated gf-alert gf-alert-danger">Only administrators can manage this page</div>';
            die();
        }
        echo '<div class="updated gf-alert gf-alert-info">';
        if (empty($_GET["post"]) and strpos($_SERVER["REQUEST_URI"], "post-new") === false):
            $gf_message = 'Use <b>Add new</b> button above to create new sql report. And click on any existing rule names below
          to manage them. ';
        else:
            $gf_message = '
       1. Give any name to your report.<br>
       2. Choose chart type, type sql query, enter field names, labels and then press to Publish/Update<br>
       3. After update you will see needed shortcode below. You can use that shortcode anywhere in your website: in pages, posts, widgets etc. <br>
        ';
        endif;
        _e('<div style="float:left">' . $gf_message . '</div>', 'guaven_sqlcharts');
        echo '<div style="float: right;
    margin-top: 0px;
    padding-top: 0px;"><a target="_blank" style="border:0px solid #6200ee;border-radius:0px;color:white;font-weight:bold;background: #6200ee;"
    class="button button-secondary" href="https://guaven.com/contact/solution-request/">Hire us for premium support</a></div> </div>';
    endif;
}
add_action('admin_notices', 'guaven_sqlcharts_my_admin_notice');




function guaven_sqlcharts_enqueue_chart()
{
    wp_enqueue_script('guaven_sqlcharts_chartjs', plugins_url('asset/bundle.min.js?v=2.1.0', __FILE__));
    wp_enqueue_script('guaven_sqlcharts_googlechart', 'https://www.gstatic.com/charts/loader.js');
    wp_enqueue_style('guaven_sqlcharts_main_style', plugins_url('asset/guaven_sqlcharts.css?v=2.1.0', __FILE__));

}
add_action('wp_enqueue_scripts', 'guaven_sqlcharts_enqueue_chart');
add_action('admin_enqueue_scripts', 'guaven_sqlcharts_enqueue_chart');

function guaven_sqlcharts_enqueue_main_style()
{
    wp_enqueue_style('guaven_sqlcharts_main_style', plugins_url('asset/guaven_sqlcharts.css?v=2.1.0', __FILE__));
}
add_action('admin_enqueue_scripts', 'guaven_sqlcharts_enqueue_main_style');


function guaven_sqlcharts_isJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

add_action('init', 'guaven_sqlcharts_register_post');
function guaven_sqlcharts_register_post()
{
    //register_taxonomy('guaven_update_push_tag', 'termin');
    register_post_type('gvn_schart', array(
        'labels' => array(
            'name' => __('My SQL Charts'),
            'singular_name' => __('My SQL chart')
        ),

        'public' => true,
        //'taxonomies' => array('guaven_update_push_tag'),
        'supports' => array(
            'title',
            'postmeta'
        ),
        'register_meta_box_cb' => 'guaven_sqlcharts_metabox_area'
    ));

    guaven_sqlcharts_load_defaults();
}

add_action('admin_footer', 'guaven_sqlcharts_admin_front');


function guaven_sqlcharts_admin_front()
{
    global $post;
    if (!empty($post) and $post->post_type == 'gvn_schart') {
?>
<style type="text/css">#normal-sortables{display: none}</style>
  <?php
    }
}

// metabox for editor
function guaven_sqlcharts_metabox_area()
{
    add_meta_box('guaven_sqlcharts_metabox', 'Configure your graph chart', 'guaven_sqlcharts_metabox', 'gvn_schart', 'advanced', 'default');
}

function guaven_sqlcharts_metabox()
{
    require_once(dirname(__FILE__) . "/admin_metabox.php");

}



function guaven_sqlcharts_save_metabox_area($post_id, $post)
{
    if (!isset($_POST['meta_box_nonce_field']) or !wp_verify_nonce($_POST['meta_box_nonce_field'], 'meta_box_nonce_action')) {
        return $post->ID;
    }
    $fields = array(
        "guaven_sqlcharts_chartheight",
        "guaven_sqlcharts_chartwidth",
        "guaven_sqlcharts_graphtype",
        "guaven_sqlcharts_xarg_s",
        "guaven_sqlcharts_xarg_l",
        "guaven_sqlcharts_yarg_s",
        "guaven_sqlcharts_yarg_l",
        "guaven_sqlcharts_tablepart",
        "guaven_sqlcharts_variables",
        "guaven_sqlcharts_formpartrole",
        "guaven_sqlcharts_formpartbutton"
    );
    foreach ($fields as $key => $value) {
        update_post_meta($post->ID, $value, esc_attr($_POST[$value]));
    }
    update_post_meta($post->ID, 'guaven_sqlcharts_code', esc_attr(str_replace("'",'"',stripslashes($_POST['guaven_sqlcharts_code']))) );
}
add_action('save_post', 'guaven_sqlcharts_save_metabox_area', 1, 2);
// save the custom fields




function guaven_sqlcharts_libloads($type, $step)
{
    $stty = array(
        'bar' => array(
            'packages' => "'corechart', 'bar'",
            'charts' => "BarChart"
        ),
        'column' => array(
            'packages' => "'corechart', 'bar'",
            'charts' => "ColumnChart"
        ),
        'area' => array(
            'packages' => "'corechart'",
            'charts' => "AreaChart"
        ),
        'pie' => array(
            'packages' => "'corechart'",
            'charts' => "PieChart"
        ),
        '3dpie' => array(
            'packages' => "'corechart'",
            'charts' => "PieChart"
        )
    );

    return $stty[$type][$step];
}
function gvn_chart_check_sql_query($sql)
{
    $blacklister   = array(
        "delete",
        "update",
        "insert",
        "drop",
        "truncate",
        "alter"
    ); //add all
    $blacklister_f = 0;
    foreach ($blacklister as $key => $value) {
        if (strpos($sql, $value) !== false)
            $blacklister_f = 1;
    }
    return $blacklister_f;
}

function guaven_get_labels_and_values($id, $fvs)
{
    $values   = array();
    $labels   = array();
    $xarg_s   = get_post_meta($id, 'guaven_sqlcharts_xarg_s', true);
    $xarg_l   = get_post_meta($id, 'guaven_sqlcharts_xarg_l', true);
    $yarg_s   = get_post_meta($id, 'guaven_sqlcharts_yarg_s', true);
    $yarg_l   = get_post_meta($id, 'guaven_sqlcharts_yarg_l', true);
    $chartype = array(
        'line_l' => 'Line',
        'pie_l' => 'Pie',
        'donut_l' => 'Pie',
        'bar_l' => 'Bar',
        'horizontalbar_l' => 'Horizontal Bar',
        'area_l' => 'Line'
    );
    foreach ($fvs as $key => $value) {
        $values[] = $value->$yarg_s;
        $labels[] = '"' . $value->$xarg_s . '"';
    }
    return array(
        $labels,
        $values,
        explode(";", $yarg_l),
        explode(";", $xarg_l)
    );
}

function guaven_sqlcharts_print_chart_js($tip_g, $title, $labels, $values, $ylabel)
{
    if ($tip_g == 'line_l') {
        guaven_sqlcharts_linedata($title, $labels, $values, $ylabel);
    }
    if ($tip_g == 'area_l') {
        guaven_sqlcharts_linedata($title, $labels, $values, $ylabel, 'true');
    } elseif ($tip_g == 'pie_l') {
        guaven_sqlcharts_piedata($title, $labels, $values, $ylabel);
    } elseif ($tip_g == 'donut_l') {
        guaven_sqlcharts_piedata($title, $labels, $values, $ylabel, 'doughnut');
    } elseif ($tip_g == 'bar_l') {
        guaven_sqlcharts_bardata($title, $labels, $values, $ylabel);
    } elseif ($tip_g == 'horizontalbar_l') {
        guaven_sqlcharts_bardata($title, $labels, $values, $ylabel, 'horizontalBar');
    }
}

function gvn_chart_put_variables($sql,$pid){
  $sql_initial=$sql;
  $variables_raw=get_post_meta($pid,'guaven_sqlcharts_variables',true);
  $variables_arr=explode("|",$variables_raw);
  foreach($variables_arr as $varfield){
    $varfield_arr=explode("~",$varfield);
    if (count($varfield_arr)<3) continue;
    $varfield_arr=array_map("trim",$varfield_arr);
    if (!empty($_GET[$varfield_arr[0]])) $varreplacement=$_GET[$varfield_arr[0]]; else $varreplacement=$varfield_arr[1];
    if (!is_numeric($varreplacement) and strpos($varreplacement,'()')===false) $varreplacement='"'.$varreplacement.'"';

    $sql_initial=str_replace('{'.$varfield_arr[0].'}',$varreplacement,$sql_initial);
  }
  //echo $sql_initial;
  return $sql_initial;
}

function gvn_chart_top_form($atts){
  if (get_post_meta($atts["id"],'guaven_sqlcharts_formpartrole',true)!='' and !is_user_logged_in()) return;
  $topform='';$dateexists=false;
  $variables_raw=get_post_meta($atts['id'],'guaven_sqlcharts_variables',true);
  $variables_raw=explode("|",$variables_raw);
  foreach ($variables_raw as $vrow){
    $vrow_arr=explode("~",$vrow);
    $vrow_arr=array_map("trim",$vrow_arr);
    if (empty($vrow_arr[3])) continue;
    $gvalue=!empty($_GET[$vrow_arr[0]])?esc_attr(urldecode($_GET[$vrow_arr[0]])):'';
    $dvalue=(strpos($vrow_arr[1],'()')===false)?esc_attr($vrow_arr[1]):'';
    if ($vrow_arr[3]=='date') {
      $dateexists=true;
      $topform.= $vrow_arr[2].' <input style="max-width:210px" type="text"
      value="'.$gvalue.'"
      data-toggle="datepicker" name="'.$vrow_arr[0].'" placeholder="'.$dvalue.'">
      ';}
    else {
      $topform.= $vrow_arr[2].' <input style="max-width:210px;'.($vrow_arr[3]=='number'?'width:100px;':'').'"
      type="'.$vrow_arr[3].'"
      value="'.$gvalue.'"    name="'.$vrow_arr[0].'" placeholder="'.$dvalue.'">
      ';
    }
  }
  if (!empty($topform)) {
    $topform='<form method="get" action="" class="guaven_sqlcharts_form">'.$topform.'
    <input type="submit"
    value="'.(get_post_meta($atts['id'], 'guaven_sqlcharts_formpartbutton', true)!=''?esc_attr(get_post_meta($atts['id'], 'guaven_sqlcharts_formpartbutton', true)):'OK').'"></form>';
    if ($dateexists) $topform.='<script>setTimeout(function(){jQuery(\'[data-toggle="datepicker"]\').datepicker({format: \'yyyy-mm-dd\'});
},300);</script>';
    return $topform ;
  }
  return;
}

function guaven_sqlcharts_local_shortcode($atts)
{
    global $wpdb;
    $sql           = html_entity_decode(get_post_meta($atts['id'], 'guaven_sqlcharts_code', true));
    $sql=gvn_chart_put_variables($sql,$atts['id']);
    $blacklister_f = gvn_chart_check_sql_query($sql);
    if ($blacklister_f == 1)
        return 'You given SQL code contains forbidden commands. Remember that you should only use SELECT queries';
    $tip_g = get_post_meta($atts['id'], 'guaven_sqlcharts_graphtype', true);

    for($i=1;$i<20;$i++){ $replacearg=!empty($atts["arg".$i])?$atts["arg".$i]:0;
        $sql=str_replace("{arg".$i."}",esc_sql($replacearg),$sql);}
    $sql_split         = explode(';', $sql);
    $labels_and_values = array();
    $post_g            = get_post($atts['id']);

    global $sqlcharts_inserted_script;
    for ($i = 0; $i < count($sql_split); $i++) {
        if (!empty($sql_split[$i])) {

            $fvs = $wpdb->get_results($sql_split[$i]);
            $wpdb->show_errors();
            ob_start();
            $wpdb->print_error();
            $printerror = ob_get_clean();
            if ($printerror != '' and strpos($printerror, "[]") === false)
                return $printerror;
            elseif (empty($fvs))
                return 'Your SQL returnes empty date, please recheck your SQL query above';

            ob_start();

            if (empty($sqlcharts_inserted_script))
                $sqlcharts_inserted_script = 1;
            $labels_and_values[$i] = guaven_get_labels_and_values($atts['id'], $fvs);
            $labels[$i]            = $labels_and_values[$i][0];
            $values[$i]            = $labels_and_values[$i][1];
            $ylabel[$i]            = !empty($labels_and_values[$i][2][$i]) ? $labels_and_values[$i][2][$i] : '';
            $xlabel[$i]            = !empty($labels_and_values[$i][3][$i]) ? $labels_and_values[$i][3][$i] : '';
        }
    }

echo gvn_chart_top_form($atts);
?>
 <canvas id="ct-chart_<?php
    echo $sqlcharts_inserted_script;
?>" width="400" height="400"></canvas>

     <script type="text/javascript">
     var ctx = jQuery("#ct-chart_<?php
    echo $sqlcharts_inserted_script;
?>");

<?php
    guaven_sqlcharts_print_chart_js($tip_g, $post_g->post_title, $labels, $values, $ylabel);
?>
</script>

<?php
    if (!empty($atts["table"])) echo guaven_sqlcharts_tablepart($post_g->post_title, $labels, $values, $ylabel,$xlabel);
    $sqlcharts_inserted_script++;
    return ob_get_clean();
}

add_shortcode('gvn_schart_2', 'guaven_sqlcharts_local_shortcode');



function guaven_sqlcharts_bardata($title, $labels, $values, $ylabel, $type = 'bar')
{
?>
    var data = {
    labels: [<?php
    echo implode(",", $labels[0]);
?>],
    datasets: [
    <?php
    for ($i = 0; $i < count($values); $i++) {
?>
        {
            label: "<?php
        echo esc_attr($ylabel[$i]);
?>",
            backgroundColor: [
                <?php
        echo guaven_sqlcharts_colorgenerator(count($values[$i]), 0, 0, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            ],
            borderColor: [
                <?php
        echo guaven_sqlcharts_colorgenerator(count($values[$i]), 0, 0.2, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            ],
            borderWidth: 1,
            data: [<?php
        echo implode(",", $values[$i]);
?>],
        },
        <?php
    }
?>
    ]
};
var options=[];
var myBarChart = new Chart(ctx, {
    type: '<?php
    echo $type;
?>',
    data: data,
    options: options
});
    <?php
}



function guaven_sqlcharts_linedata($title, $labels, $values, $ylabel, $type = 'false')
{
?>
var data = {
    labels: [<?php
    echo implode(",", $labels[0]);
?>],
    datasets: [
    <?php
    for ($i = 0; $i < count($values); $i++) {
?>
        {
            label: "<?php
        echo esc_attr($ylabel[$i]);
?>",
            fill: <?php echo $type=="false"?$type:($i==0?'"+1"':'"origin"');
?>,
            lineTension: 0.1,
            backgroundColor:  <?php
        echo guaven_sqlcharts_colorgenerator(1, 1, 0.2, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            borderColor:  <?php
        echo guaven_sqlcharts_colorgenerator(1, 1, 0.2, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            pointBorderColor: <?php
        echo guaven_sqlcharts_colorgenerator(1, 1, 0.2, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            pointHoverBackgroundColor: <?php
        echo guaven_sqlcharts_colorgenerator(1, 1, 0.2, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            pointHoverBorderColor:  <?php
        echo guaven_sqlcharts_colorgenerator(1, 1, 0.2, rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255));
?>
            data: [<?php
        echo implode(",", $values[$i]);
?>],
            spanGaps: false,
        },
        <?php
    }
?>
    ]
};
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: data,
   options: {
        scales: {
            xAxes: [{
                display: false
            }]
        }
    }
});
    <?php
}


function guaven_sqlcharts_piedata($title, $labels, $values, $ylabel, $type = 'pie')
{
?>
    var options=[];
    var data = {
    labels: [<?php
    echo implode(",", $labels[0]);
?>],
    datasets: [
    <?php
    for ($i = 0; $i < count($values); $i++) {
?>
        {
            data: [<?php
        echo implode(",", $values[$i]);
?>],
            backgroundColor: [
                <?php
        echo guaven_sqlcharts_colorgenerator(count($labels[$i]), 1);
?>
            ],
            hoverBackgroundColor: [
               <?php
        echo guaven_sqlcharts_colorgenerator(count($labels[$i]), 1);
?>
            ]
        },
<?php
    }
?>
        ]
};
var myPieChart = new Chart(ctx,{
    type: '<?php
    echo $type;
?>',
    data: data,
    options: options
});
    <?php
}




function guaven_sqlcharts_colorgenerator($count, $indic, $darkness = 0, $initcolor = '255,0,0')
{
    $initial_colors = array(
        'linebg' => 'red',
        'linebr' => 'yellow',
        'linebc' => 'green',
        'linehbg' => 'white',
        'linehbc' => 'black'
    );
    if (!empty($initial_colors[$count]))
        return '"' . $initial_colors[$count] . '",
        ';
    $ret = '';
    for ($i = 0; $i < $count; $i++) {
        $ret .= "'rgba(" . $initcolor . "," . ($darkness + 0.8 - $indic * $i * 0.8 / ($count)) . ")',
        ";
    }
    return $ret;
}

function guaven_sqlcharts_tablepart($title, $labels, $values, $ylabel,$xlabel){
  $tabledata='';
  foreach($values as $valuerow){
    $tablein='<br>';
    foreach ($valuerow as $key => $value) {
      $tablein.='<tr><td>'.str_replace('"',"",$labels[0][$key]).'</td><td>'.$value.'</td></tr>';
    }
    $tabledata.='<table><tr><th>'.$xlabel[0].'</th><th>'.$ylabel[0].'</th></tr>'.$tablein.'</table><br>';
  }

  return $tabledata;

}
