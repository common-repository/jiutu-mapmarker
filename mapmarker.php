<?php
/*
Plugin Name: 旅行地图
Plugin URI: https://wordpress.org/plugins/jiutu-mapmarker/
Description: 一款精美的旅行地图标记,记录日志插件!
Version:1.2.7
Author: 不问归期_
Author URI: https://www.aliluv.cn/
Tags: jiutu-mapmarker, 地图, 旅行地图, 地图标记, 地点标记 ,Marker ,Map Marker,Map
*/


require_once plugin_dir_path(__FILE__) . 'inc/classes/setup.class.php';

if (class_exists('CSF')) {
   $prefix = 'jiutu_mapmarker_data';
   CSF::createOptions($prefix, array(
      'framework_title'   => '旅行地图 ' . '<small> 一款精美的旅行地图标记,记录日志插件!</small>',
      'show_search'       => false,
      'theme'             => 'light',
      'menu_title'        => '旅行地图',
      'menu_icon'         => 'dashicons-location-alt',
      'menu_slug'         => 'jiutu_mapmarker',
      'footer_text'       => '任何使用问题可联系QQ:781272314',
      'nav'               => 'inline',
      'show_reset_all'    => false,
      'show_reset_section' => false,
      'show_all_options'  => false,
   ));
   CSF::createSection($prefix, array(
      'title'  => '我的足迹',
      'fields' => array(
         array(
            'id'        => 'mapmarker_gojson',
            'type'      => 'group',
            'accordion_title_number' => true,
            'accordion_title_by' => array('title', 'address'),
            'accordion_title_by_prefix' => '_',

            'fields'    => array(
               array(
                  'id'    => 'title',
                  'type'  => 'text',
                  'title' => '标记标题',
                  'placeholder'  => '说点什么吧',
               ),

               array(
                  'id'           => 'images',
                  'type'         => 'upload',
                  'title'        => '标记缩略图',
                  'library'      => 'image',
                  'placeholder'  => 'https://',
                  'preview'      => true,
                  'button_title' => '添加图片',
                  'remove_title' => '移除',
               ),

               // Select with AJAX search Pages
               // Select with CPT (custom post type) pages
               array(
                  'id'          => 'posts',
                  'type'        => 'select',
                  'title'       => '文章',
                  'chosen'      => true,
                  'multiple'    => true,
                  'placeholder' => '旅行文章',
                  'options'     => 'posts',
               ),
               array(
                  'id'      => 'markerColour',
                  'type'    => 'color',
                  'title'   => '标记颜色',
                  'default' => '#3a6fbe'
               ),

               // array(
               //     'id'          => 'map_data',
               //     'type'        => 'map',
               //     'title'       => '标记地点',
               //     'default'     => array(
               //         'address'   => '中国北京首都',
               //         'latitude'  => '39.91647700153368',
               //         'longitude' => '116.390789451959',
               //         'zoom'      => '5',
               //     ),
               // ),
               array(
                  'id'    => 'description',
                  'type'  => 'wp_editor',
                  'title' => '介绍/说明',
               ),

               array(
                  'id'       => 'map_time',
                  'type'     => 'date',
                  'title'    => '日期',
                  'settings' => array(
                     'dateFormat'      => 'yy/mm/dd',
                     'changeMonth'     => true,
                     'changeYear'      => true,
                     'showWeek'        => true,
                     'showButtonPanel' => true,
                     'weekHeader'      => '周期',
                     'monthNamesShort' => array('一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十月', '十二月'),
                     'dayNamesMin'     => array('星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'),
                  ),
                  'default'  => date("Y/m/d", time()),
               ),
               array(
                  'id'            => 'address',
                  'type'          => 'text',
                  'title'         => '标记地点名称',
                  'desc'          => '<strong>在此输入地点后,下方地图自动定位</strong> ',
               ),

               array(
                  'id'            => 'map_data',
                  'type'          => 'map',
                  'desc'          => '<strong>拖动地图的 ·标记· 可选择地点</strong> ',
                  'address_field' => 'address',
                  'height'   => '500px',
                  'default'     => array(
                     'latitude'  => '39.91647700153368',
                     'longitude' => '116.390789451959',
                     'zoom'      => '5',
                  ),
               ),

            ),
         )
      )
   ));

   // $options = get_permalink(1);
   // var_dump($options);
   // echo get_the_title(1);
   CSF::createSection($prefix, array(
      'title'  => '地图设置',
      'fields' => array(
         array(
            'id'     => 'jiutu_map_settings',
            'type'   => 'fieldset',
            'fields' => array(
               array(
                  'id'        => 'map_style',
                  'type'      => 'image_select',
                  'class'     => 'map_hero-img-wrap',
                  'options'   => array(
                     'mapbox://styles/jiutu/cl4tuyb9y000o14rmpgxchscd' => '/wp-content/plugins/jiutu-mapmarker/static/css/images/map/light.png',
                     // 'mapbox://styles/mapbox/dark-v10' => '/wp-content/plugins/jiutu-mapmarker/static/css/images/map/dark.png',
                     // 'mapbox://styles/mapbox/outdoors-v11' => '/wp-content/plugins/jiutu-mapmarker/static/css/images/map/outdoors.png',
                     // 'mapbox://styles/mapbox/streets-v11' => '/wp-content/plugins/jiutu-mapmarker/static/css/images/map/streets.png',
                  ),
                  'default'   => 'mapbox://styles/jiutu/cl4tuyb9y000o14rmpgxchscd',
                  'desc'  => '默认打开页面显示的样式'
               ),
               array(
                  'id'       => 'map_type',
                  'type'     => 'button_set',
                  'title'    => '地图类型',
                  'options'  => array(
                     'globe' => '球形',
                     'plane' => '平面',
                  ),
                  'default'  => 'globe',
               ),
               array(
                  'id'        => 'map_star',
                  'type'      => 'fieldset',
                  'title'     => '球形星空地图设置',
                  'fields'    => array(
                     array(
                        'id'        => 'color',
                        'type'      => 'color_group',
                        'options'   => array(
                           'color' => '地球外表颜色',
                           'high_color' => '大气层颜色',
                           'space_color' => '星空颜色',
                        ),
                        'default'   => array(
                           'color' => '#ffffff',
                           'high_color' => '#245cdf',
                           'space_color' => '#010b19',
                        ),
                        'desc'  => '根据喜好调节颜色.也可使用默认颜色'
                     ),
                     array(
                        'id'          => 'fog',
                        'type'        => 'dimensions',
                        'show_units'  => false,
                        'width_icon'    => '星星大小', //
                        'height_icon'  => '大气氛围大小', //
                        'default'     => array(
                           'width'      => '0.3',
                           'height'    => '0.019',
                        ),
                        'desc'  => '`星星大小`数值0~1之间,默认0.3、`大气氛围大小`数值0~1之间,默认0.019'
                     ),
                  ),
                  // 'default'        => array(
                  //    'opt-text'     => 'Text default value',
                  //    'opt-color'    => '#ffbc00',
                  //    'opt-switcher' => true,
                  // ),
                  'dependency' => array(array('map_type', '==', 'globe')),
               ),
               array(
                  'id'       => 'map_language',
                  'type'     => 'button_set',
                  'title'    => '地图显示语言',
                  'options'  => array(
                     'zh-Hans' => '简体中文',
                     'zh-Hant' => '繁体中文',
                     'en' => '英语',
                     'fr' => '法语',
                     'de' => '德语',
                     'ko' => '韩语',
                  ),
                  'default'  => 'zh-Hans',
               ),
               array(
                  'id'      => 'map_size',
                  'type'    => 'border',
                  'title'   => '地图显示设置',
                  'top_icon'  => '高度',
                  'right_icon'   => '宽度',
                  'bottom_icon'  => '边框大小',
                  'style'  => false,
                  'left' => false,
                  'default' => array(
                     'top'    => '600',
                     'right'   => '100',
                     'bottom' => '3',
                     'color'  => '#bfbfbf',
                  ),
                  'desc'  => '`宽度` 建议 1~100之间'
               ),



               array(
                  'id'       => 'map_zoom_control',
                  'type'     => 'button_set',
                  'title'    => '地图缩放控件',
                  'options'  => array(
                     'display'   => '禁用',
                     'top-left'   => '左上角',
                     'top-right' => '右上角',
                     'bottom-left' => '左下角',
                     'bottom-right'   => '右下角',
                  ),
                  'default'  => 'top-right',
                  'desc'  => '前端地图缩放控件位置设置,·禁用·则不显示'
               ),

               array(
                  'id'       => 'map_location',
                  'type'     => 'button_set',
                  'title'    => '地图归位控件',
                  'options'  => array(
                     'display'   => '禁用',
                     'top-left'   => '左上角',
                     'top-right' => '右上角',
                     'bottom-left' => '左下角',
                     'bottom-right'   => '右下角',
                  ),
                  'default'  => 'top-right',
                  'desc'  => '前端地图归位控件位置设置,·禁用·则不显示'
               ),

               array(
                  'id'    => 'map_initial_zoom',
                  'type'  => 'spinner',
                  'title' => '地图初始缩放',
                  'min'     => 0,
                  'max'     => 24,
                  'step'    => 1,
                  'default' => 3,
                  'desc'  => '地图的初始·缩放·级别(0~24)',
               ),

               array(
                  'id'       => 'map_zoom',
                  'type'     => 'dimensions',
                  'title'    => '地图缩放控制',
                  'show_units'   => false,
                  'width_icon'   => '最小缩放级别',
                  'height_icon'  => '最大缩放级别',
                  'default'  => array(
                     'width'  => 0,
                     'height' => 24,
                  ),
                  'desc'  => '控制地图的·缩放·程度(0~24)',
               ),

               array(
                  'id'       => 'map_center',
                  'type'     => 'map',
                  'title'    => '地图初始中心位置',
                  'height'   => '300px',
                  'settings' => array(
                     'scrollWheelZoom' => true,
                  ),
                  'default'     => array(
                     'address'   => '这里可以查找你要的地址呢',
                     'latitude'  => 36.01948357661335,
                     'longitude' => 102.88378351275336,
                     'zoom'      => '2',
                  )
               ),
               array(
                  'id'    => 'map_desc',
                  'type'  => 'wp_editor',
                  'title' => '地图下方介绍',
               ),
            ),
         ),
      )
   ));

   CSF::createSection($prefix, array(
      'title'  => '使用说明',
      'fields' => array(
         array(
            'type'     => 'callback',
            'function' => 'jiutu_instructions',
         ),
      )
   ));
}
// A Callback function
function jiutu_instructions()
{ ?>

   <div class="wrap">
      <style>
         .community-events-form .regular-text {
            width: 65%;
            height: 29px;
            margin: 0;
            vertical-align: top;
         }


         .welcome-panel::before {
            content: "";
            position: absolute;
            top: -16px;
            right: 96px;
            z-index: 0;
            width: 300px;
            height: 382px;
            background: url(<?php echo plugins_url('/static/about-header-about.png', __FILE__); ?>) no-repeat center;
            background-size: contain;

         }
      </style>
      <div id="welcome-panel" class="welcome-panel">
         <div class="welcome-panel-content">
            <div class="welcome-panel-header">
               <h2>欢迎使用JiuTuMapmarker!</h2>
            </div>
            <div class="welcome-panel-column-container">
               <div class="welcome-panel-column">
                  <div class="welcome-panel-icon-pages"></div>
                  <div class="welcome-panel-column-content">
                     <h3>高度自定义</h3>
                     <p>地点、图片、分享、地图配置</p>
                  </div>
               </div>
               <div class="welcome-panel-column">
                  <div class="welcome-panel-icon-layout"></div>
                  <div class="welcome-panel-column-content">
                     <h3>极度适配</h3>
                     <p>自适应市面上的主题</p>
                  </div>
               </div>
               <div class="welcome-panel-column">
                  <div class="welcome-panel-icon-styles"></div>
                  <div class="welcome-panel-column-content">
                     <h3>简单、快捷</h3>
                     <p>随时随地都能分享</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div id="dashboard-widgets-wrap">
         <div id="dashboard-widgets" class="metabox-holder">
            <div id="postbox-container-1" class="postbox-container">
               <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                  <div id="dashboard_primary" class="postbox ">
                     <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle">简单的配置</h2>

                     </div>
                     <div class="inside">
                        <div class="community-events"><br />

                           <ul class="community-events-results activity-block last">
                              <li class="event-none">
                                 一、设置一个展示地图的 页面
                              </li>
                           </ul>
                        </div>


                        <div class="wordpress-news hide-if-no-js">
                           <div class="rss-widget">
                              <ul>
                                 <li class="rsswidget">1、先复制 <code> [jiutu_mapmarker_shortcode]</code> 此段短代码!(·中括号·也要一起复制)</li>
                                 <li>2、点击 <a href="<?php echo admin_url("post-new.php"); ?>?post_type=page" target="_blank">新增页面</a> 标题随意! 内容:粘贴上面的 短代码 后保存即可!</li>
                                 <li>3、访问页面即可显示地图</li>
                              </ul>
                           </div>

                        </div>
                        <div class="community-events"><br />

                           <ul class="community-events-results activity-block last">
                              <li class="event-none">
                                 二、由于插件使用付费地图资源包、故需授权(授权域名)才可正常使用!!<br />
                                 前往 <a href="https://wpapi.aliluv.cn/wp-admin/admin.php?page=jiutu_mapmarker_admin">WP管理平台</a>
                                 进行授权:登录账号(没有账号请注册)->旅行地图
                              </li>
                           </ul>
                        </div>
                        <p class="community-events-footer">
                           <a href="https://wpapi.aliluv.cn/maps" target="_blank">
                              查看演示
                              <span aria-hidden="true" class="dashicons dashicons-external"></span>
                           </a>
                        </p>

                     </div>
                  </div>

               </div>
            </div>
            <div class="postbox-container">
               <div class="meta-box-sortables ui-sortable">
                  <div class="postbox  hide-if-js" style="display: block;">
                     <div class="postbox-header">
                        <h2 class="hndle ui-sortable-handle">插件介绍</h2>
                     </div>
                     <div class="inside">
                        <div class="main">
                           <ul>
                              <li class="comment-count">
                                 <h2> 一款精美的旅行地图标记,记录日志插件! </h2>
                              </li>
                              <li class="page-count">
                                 <h4> 帮助用户记录旅途中经过的每一个地点，点亮足迹地图，让你可以清晰的看到自己去过哪些地方，还可以储存你在旅途中拍摄的照片，和别人分享你美好的行程!</h4>
                              </li>
                           </ul>
                           <p id="wp-version-message"><span id="wp-version">任何问题联系QQ: 781272314</span></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

<?php
}




add_filter("plugin_action_links_" . plugin_basename(__FILE__), function ($links) {
   $settings_link = '<a href="/wp-admin/admin.php?page=jiutu_mapmarker">' . __('Settings') . '</a>';
   $api_link = '<a href="https://wpapi.aliluv.cn/wp-admin/admin.php?page=jiutu_mapmarker_admin">授权</a>';
   array_push($links, $settings_link, $api_link);
   return $links;
});

function jiutu_mapmarker_shortcode_init()
{
   wp_enqueue_style('mapmarkerCSS1', plugins_url('/static/css/mapbox-gl.css', __FILE__));
   wp_enqueue_script('mapmarkerJS1', plugins_url('/static/js/mapbox-gl.js', __FILE__), array(), false, true);
   wp_enqueue_script('jiutu_mapmarker_js_library', plugins_url('/static/js/mapbox.js', __FILE__), array(), false, true);
   add_shortcode("jiutu_mapmarker_shortcode", function () {

      if (is_admin()) {
         return '';
      }
      return  '<br><div class="jiutu-map"><div id="jiutu_mapmarker_map"></div></div><br><div id="map_desc"></div>';
   });
}
add_action('init', 'jiutu_mapmarker_shortcode_init');




/**
 * 插件激活期间运行的代码。
 *
 * @return  [type]  [return description]
 */
register_activation_hook(__FILE__, function () {
   jiutu_mapmarker_weixin_send('旅行地图插件被激活');
});


/**
 * 插件停用期间运行的代码。
 *
 * @return  [type]  [return description]
 */
register_deactivation_hook(__FILE__, function () {
   jiutu_mapmarker_weixin_send('旅行地图插件被停用');
});


/**
 * 微信通知
 *
 * @param   [type]  $title    [$title description]
 * @param   [type]  $content  [$content description]
 *
 * @return  [type]            [return description]
 */
function jiutu_mapmarker_weixin_send($title, $content = '通知:')
{
   $request = new WP_Http;
   $request->request('https://wpapi.aliluv.cn/wp-admin/admin-ajax.php', array(
      'method' => 'GET',
      'body' => array(
         'action' => 'jiutu_weixin_send',
         'title' => $title,
         'content' => $content . date("Y-m-d H:i:s", time())
      )
   ));
}



add_action('wp_ajax_nopriv_jiutu_mapmarker_geojson_api', 'jiutu_mapmarker_geojson_api');
add_action('wp_ajax_jiutu_mapmarker_geojson_api', 'jiutu_mapmarker_geojson_api');
function jiutu_mapmarker_geojson_api()
{
   // var_dump('sfd');
   $res = get_option('jiutu_mapmarker_data');
   if (!empty($res['mapmarker_gojson'])) {
      foreach ($res['mapmarker_gojson'] as $keys =>  $value) {
         if (isset($value['posts'])) {
            foreach ($value['posts'] as $key =>  $val) {
               $link = get_permalink($val);
               $title = get_the_title($val);
               $posts[$key] = array($link => $title);
            }
            $res['mapmarker_gojson'][$keys]['posts'] =  $posts;
            unset($posts);
         }
      }
   }
   exit(json_encode($res));
}
