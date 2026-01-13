<?php
/**
 * Plugin Name: Keks - DSGVO/GDPR Cookie Banner
 * Plugin URI: https://github.com/mistermusiker/keks
 * Description: Simple, GDPR-compliant cookie banner without dark patterns.
 * Version: 1.0.0
 * Author: Roger Kirchhoff
 * License: GPL v2 or later
 * Text Domain: keks
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KEKS_VERSION', '1.0.0');
define('KEKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KEKS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Translation function for Keks plugin
 * Loads translations based on WordPress locale
 *
 * @param string $key Translation key
 * @param string $default Default value if key not found
 * @return string Translated string
 */
function keks_t($key, $default = '') {
    static $translations = null;

    if ($translations === null) {
        $locale = determine_locale();
        $lang_file = KEKS_PLUGIN_DIR . 'languages/' . $locale . '.php';

        if (file_exists($lang_file)) {
            $translations = include $lang_file;
        } else {
            // Fallback to English
            $fallback_file = KEKS_PLUGIN_DIR . 'languages/en_US.php';
            if (file_exists($fallback_file)) {
                $translations = include $fallback_file;
            } else {
                $translations = [];
            }
        }
    }

    return $translations[$key] ?? $default;
}

class Keks {

    private static $instance = null;

    /**
     * Default cookie categories
     */
    private $default_categories = [
        'necessary' => [
            'name_key' => 'category_necessary_name',
            'desc_key' => 'category_necessary_desc',
            'required' => true,
        ],
        'statistics' => [
            'name_key' => 'category_statistics_name',
            'desc_key' => 'category_statistics_desc',
            'required' => false,
        ],
        'marketing' => [
            'name_key' => 'category_marketing_name',
            'desc_key' => 'category_marketing_desc',
            'required' => false,
        ],
    ];

    /**
     * Known tracking services with preconfigured scripts
     */
    private $known_services = [
        // === STATISTICS ===
        'google_analytics_4' => [
            'name' => 'Google Analytics 4',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_measurement_id',
            'id_placeholder' => 'G-XXXXXXXXXX',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://www.googletagmanager.com/gtag/js?id={ID}'],
                ['type' => 'inline', 'template' => "window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{ID}');"],
            ],
        ],
        'google_tag_manager' => [
            'name' => 'Google Tag Manager',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_container_id',
            'id_placeholder' => 'GTM-XXXXXXX',
            'scripts' => [
                ['type' => 'inline', 'template' => "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{ID}');"],
            ],
        ],
        'matomo' => [
            'name' => 'Matomo (Piwik)',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_matomo_url',
            'id_placeholder' => 'https://analytics.example.com/',
            'id2_label_key' => 'service_site_id',
            'id2_placeholder' => '1',
            'scripts' => [
                ['type' => 'inline', 'template' => "var _paq=window._paq=window._paq||[];_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);(function(){var u='{ID}';_paq.push(['setTrackerUrl',u+'matomo.php']);_paq.push(['setSiteId','{ID2}']);var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];g.async=true;g.src=u+'matomo.js';s.parentNode.insertBefore(g,s);})();"],
            ],
        ],
        'hotjar' => [
            'name' => 'Hotjar',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_site_id',
            'id_placeholder' => '1234567',
            'scripts' => [
                ['type' => 'inline', 'template' => "(function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};h._hjSettings={hjid:{ID},hjsv:6};a=o.getElementsByTagName('head')[0];r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r);})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');"],
            ],
        ],
        'microsoft_clarity' => [
            'name' => 'Microsoft Clarity',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_project_id',
            'id_placeholder' => 'abcdefghij',
            'scripts' => [
                ['type' => 'inline', 'template' => "(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,'clarity','script','{ID}');"],
            ],
        ],
        'plausible' => [
            'name' => 'Plausible Analytics',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_domain',
            'id_placeholder' => 'example.com',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://plausible.io/js/script.js', 'attrs' => 'data-domain="{ID}"'],
            ],
        ],
        'fathom' => [
            'name' => 'Fathom Analytics',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_site_id',
            'id_placeholder' => 'ABCDEFGH',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://cdn.usefathom.com/script.js', 'attrs' => 'data-site="{ID}"'],
            ],
        ],
        'etracker' => [
            'name' => 'etracker',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_secure_code',
            'id_placeholder' => 'xxxxxx',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://code.etracker.com/code/e.js', 'attrs' => 'data-secure-code="{ID}"'],
            ],
        ],
        'mouseflow' => [
            'name' => 'Mouseflow',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_website_id',
            'id_placeholder' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "window._mfq=window._mfq||[];(function(){var mf=document.createElement('script');mf.type='text/javascript';mf.defer=true;mf.src='//cdn.mouseflow.com/projects/{ID}.js';document.getElementsByTagName('head')[0].appendChild(mf);})();"],
            ],
        ],
        'mixpanel' => [
            'name' => 'Mixpanel',
            'category' => 'statistics',
            'position' => 'head',
            'id_label_key' => 'service_project_token',
            'id_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "(function(f,b){if(!b.__SV){var e,g,i,h;window.mixpanel=b;b._i=[];b.init=function(e,f,c){function g(a,d){var b=d.split('.');2==b.length&&(a=a[b[0]],d=b[1]);a[d]=function(){a.push([d].concat(Array.prototype.slice.call(arguments,0)))}}var a=b;'undefined'!==typeof c?a=b[c]=[]:c='mixpanel';a.people=a.people||[];a.toString=function(a){var d='mixpanel';'mixpanel'!==c&&(d+='.'+c);a||(d+=' (stub)');return d};a.people.toString=function(){return a.toString(1)+'.people (stub)'};i='disable time_event track track_pageview track_links track_forms track_with_groups add_group set_group remove_group register register_once alias unregister identify name_tag set_config reset opt_in_tracking opt_out_tracking has_opted_in_tracking has_opted_out_tracking clear_opt_in_out_tracking start_batch_senders people.set people.set_once people.unset people.increment people.append people.union people.track_charge people.clear_charges people.delete_user people.remove'.split(' ');for(h=0;h<i.length;h++)g(a,i[h]);var j='set set_once union unset remove delete'.split(' ');a.get_group=function(){function b(c){d[c]=function(){call2_args=arguments;call2=[c].concat(Array.prototype.slice.call(call2_args,0));a.push([e,call2])}}for(var d={},e=['get_group'].concat(Array.prototype.slice.call(arguments,0)),c=0;c<j.length;c++)b(j[c]);return d};b._i.push([e,f,c])};b.__SV=1.2;e=f.createElement('script');e.type='text/javascript';e.async=!0;e.src='undefined'!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:'file:'===f.location.protocol&&'//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js'.match(/^\\/\\//)  ?'https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js':'//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js';g=f.getElementsByTagName('script')[0];g.parentNode.insertBefore(e,g)}})(document,window.mixpanel||[]);mixpanel.init('{ID}',{batch_requests:true});"],
            ],
        ],

        // === MARKETING ===
        'facebook_pixel' => [
            'name' => 'Facebook Pixel (Meta)',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_pixel_id',
            'id_placeholder' => '123456789012345',
            'scripts' => [
                ['type' => 'inline', 'template' => "!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{ID}');fbq('track','PageView');"],
            ],
        ],
        'google_ads' => [
            'name' => 'Google Ads Conversion',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_conversion_id',
            'id_placeholder' => 'AW-123456789',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://www.googletagmanager.com/gtag/js?id={ID}'],
                ['type' => 'inline', 'template' => "window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{ID}');"],
            ],
        ],
        'linkedin_insight' => [
            'name' => 'LinkedIn Insight Tag',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_partner_id',
            'id_placeholder' => '123456',
            'scripts' => [
                ['type' => 'inline', 'template' => "_linkedin_partner_id='{ID}';window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];window._linkedin_data_partner_ids.push(_linkedin_partner_id);(function(l){if(!l){window.lintrk=function(a,b){window.lintrk.q.push([a,b])};window.lintrk.q=[]}var s=document.getElementsByTagName('script')[0];var b=document.createElement('script');b.type='text/javascript';b.async=true;b.src='https://snap.licdn.com/li.lms-analytics/insight.min.js';s.parentNode.insertBefore(b,s);})(window.lintrk);"],
            ],
        ],
        'tiktok_pixel' => [
            'name' => 'TikTok Pixel',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_pixel_id',
            'id_placeholder' => 'XXXXXXXXXX',
            'scripts' => [
                ['type' => 'inline', 'template' => "!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)};ttq.load('{ID}');ttq.page();}(window,document,'ttq');"],
            ],
        ],
        'pinterest_tag' => [
            'name' => 'Pinterest Tag',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_tag_id',
            'id_placeholder' => '1234567890123',
            'scripts' => [
                ['type' => 'inline', 'template' => "!function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version='3.0';var t=document.createElement('script');t.async=!0,t.src=e;var r=document.getElementsByTagName('script')[0];r.parentNode.insertBefore(t,r)}}('https://s.pinimg.com/ct/core.js');pintrk('load','{ID}');pintrk('page');"],
            ],
        ],
        'twitter_pixel' => [
            'name' => 'Twitter/X Pixel',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_pixel_id',
            'id_placeholder' => 'xxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "!function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);},s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='https://static.ads-twitter.com/uwt.js',a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');twq('config','{ID}');"],
            ],
        ],
        'snapchat_pixel' => [
            'name' => 'Snapchat Pixel',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_pixel_id',
            'id_placeholder' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "(function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};a.queue=[];var s='script';r=t.createElement(s);r.async=!0;r.src=n;var u=t.getElementsByTagName(s)[0];u.parentNode.insertBefore(r,u);})(window,document,'https://sc-static.net/scevent.min.js');snaptr('init','{ID}',{});snaptr('track','PAGE_VIEW');"],
            ],
        ],
        'bing_ads' => [
            'name' => 'Microsoft/Bing Ads UET',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_uet_tag_id',
            'id_placeholder' => '12345678',
            'scripts' => [
                ['type' => 'inline', 'template' => "(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:'{ID}'};o.q=w[u],w[u]=new UET(o),w[u].push('pageLoad')},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=='loaded'&&s!=='complete'||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,'script','//bat.bing.com/bat.js','uetq');"],
            ],
        ],
        'criteo' => [
            'name' => 'Criteo',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_account_id',
            'id_placeholder' => '12345',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://static.criteo.net/js/ld/ld.js', 'attrs' => 'data-criteo-account-id="{ID}"'],
            ],
        ],
        'taboola' => [
            'name' => 'Taboola Pixel',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_account_id',
            'id_placeholder' => '1234567',
            'scripts' => [
                ['type' => 'inline', 'template' => "window._tfa=window._tfa||[];window._tfa.push({notify:'event',name:'page_view',id:{ID}});!function(t,f,a,x){if(!document.getElementById(x)){t.async=1;t.src=a;t.id=x;f.parentNode.insertBefore(t,f);}}(document.createElement('script'),document.getElementsByTagName('script')[0],'//cdn.taboola.com/libtrc/unip/{ID}/tfa.js','tb_tfa_script');"],
            ],
        ],
        'outbrain' => [
            'name' => 'Outbrain Pixel',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_marketer_id',
            'id_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "!function(_window,_document){var OB_ADV_ID='{ID}';if(_window.obApi){var toArray=function(object){return Object.prototype.toString.call(object)==='[object Array]'?object:[object];};_window.obApi.marketerId=toArray(_window.obApi.marketerId).concat(toArray(OB_ADV_ID));return;}var api=_window.obApi=function(){api.dispatch?api.dispatch.apply(api,arguments):api.queue.push(arguments);};api.version='1.1';api.loaded=true;api.marketerId=OB_ADV_ID;api.queue=[];var tag=_document.createElement('script');tag.async=true;tag.src='//amplify.outbrain.com/cp/obtp.js';tag.type='text/javascript';var script=_document.getElementsByTagName('script')[0];script.parentNode.insertBefore(tag,script);}(window,document);obApi('track','PAGE_VIEW');"],
            ],
        ],

        // === CHAT & SUPPORT ===
        'intercom' => [
            'name' => 'Intercom',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_app_id',
            'id_placeholder' => 'xxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "window.intercomSettings={api_base:'https://api-iam.intercom.io',app_id:'{ID}'};(function(){var w=window;var ic=w.Intercom;if(typeof ic==='function'){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/{ID}';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();"],
            ],
        ],
        'hubspot' => [
            'name' => 'HubSpot',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_hub_id',
            'id_placeholder' => '12345678',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://js.hs-scripts.com/{ID}.js'],
            ],
        ],
        'crisp' => [
            'name' => 'Crisp Chat',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_website_id',
            'id_placeholder' => 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "window.\$crisp=[];window.CRISP_WEBSITE_ID='{ID}';(function(){d=document;s=d.createElement('script');s.src='https://client.crisp.chat/l.js';s.async=1;d.getElementsByTagName('head')[0].appendChild(s);})();"],
            ],
        ],
        'zendesk' => [
            'name' => 'Zendesk Chat',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_chat_key',
            'id_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://static.zdassets.com/ekr/snippet.js?key={ID}'],
            ],
        ],
        'livechat' => [
            'name' => 'LiveChat',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_license_id',
            'id_placeholder' => '12345678',
            'scripts' => [
                ['type' => 'inline', 'template' => "window.__lc=window.__lc||{};window.__lc.license={ID};(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:'2.0',on:function(){i(['on',c.call(arguments)])},once:function(){i(['once',c.call(arguments)])},off:function(){i(['off',c.call(arguments)])},get:function(){if(!e._h)throw new Error('[LiveChatWidget] You can\\'t use getters before load.');return i(['get',c.call(arguments)])},call:function(){i(['call',c.call(arguments)])},init:function(){var n=t.createElement('script');n.async=!0,n.type='text/javascript',n.src='https://cdn.livechatinc.com/tracking.js',t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice));"],
            ],
        ],
        'tawk' => [
            'name' => 'Tawk.to',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_property_widget_id',
            'id_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxx/xxxxxxxx',
            'scripts' => [
                ['type' => 'inline', 'template' => "var Tawk_API=Tawk_API||{},Tawk_LoadStart=new Date();(function(){var s1=document.createElement('script'),s0=document.getElementsByTagName('script')[0];s1.async=true;s1.src='https://embed.tawk.to/{ID}';s1.charset='UTF-8';s1.setAttribute('crossorigin','*');s0.parentNode.insertBefore(s1,s0);})();"],
            ],
        ],
        'tidio' => [
            'name' => 'Tidio',
            'category' => 'marketing',
            'position' => 'footer',
            'id_label_key' => 'service_public_key',
            'id_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://code.tidio.co/{ID}.js'],
            ],
        ],

        // === OTHER ===
        'recaptcha_v3' => [
            'name' => 'Google reCAPTCHA v3',
            'category' => 'necessary',
            'position' => 'head',
            'id_label_key' => 'service_site_key',
            'id_placeholder' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'scripts' => [
                ['type' => 'url', 'template' => 'https://www.google.com/recaptcha/api.js?render={ID}'],
            ],
        ],
        'youtube' => [
            'name_key' => 'service_youtube_tracking',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_video_id',
            'id_placeholder' => 'dQw4w9WgXcQ',
            'is_iframe' => true,
            'iframe_template' => 'https://www.youtube.com/embed/{ID}',
        ],
        'vimeo' => [
            'name' => 'Vimeo',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_video_id',
            'id_placeholder' => '123456789',
            'is_iframe' => true,
            'iframe_template' => 'https://player.vimeo.com/video/{ID}',
        ],
        'google_maps' => [
            'name' => 'Google Maps',
            'category' => 'marketing',
            'position' => 'head',
            'id_label_key' => 'service_embed_code',
            'id_placeholder' => 'place_id:ChIJN1t_tDeuEmsRUsoyG83frY4',
            'is_iframe' => true,
            'iframe_template' => 'https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q={ID}',
        ],
    ];

    /**
     * Gibt bekannte Dienste zurück
     */
    public function get_known_services() {
        $services = [];
        foreach ($this->known_services as $key => $service) {
            $services[$key] = $service;
            // Translate id_label_key to id_label
            if (isset($service['id_label_key'])) {
                $services[$key]['id_label'] = keks_t($service['id_label_key']);
            }
            // Translate id2_label_key to id2_label
            if (isset($service['id2_label_key'])) {
                $services[$key]['id2_label'] = keks_t($service['id2_label_key']);
            }
        }
        return $services;
    }

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render_banner']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'maybe_upgrade_db']);

        // Meta-Box für Seiten-Ausnahmen
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box']);

        // Google Consent Mode v2 (muss VOR allen Google-Scripts kommen)
        add_action('wp_head', [$this, 'render_google_consent_mode'], 1);

        // Verwaltete Scripts ausgeben
        add_action('wp_head', [$this, 'render_managed_scripts_head'], 99);
        add_action('wp_footer', [$this, 'render_managed_scripts_footer'], 5);

        // AJAX-Endpoint für Consent-Logging
        add_action('wp_ajax_keks_log_consent', [$this, 'ajax_log_consent']);
        add_action('wp_ajax_nopriv_keks_log_consent', [$this, 'ajax_log_consent']);

        // Shortcode für Widerruf-Link
        add_shortcode('keks_revoke', [$this, 'shortcode_revoke_link']);
    }

    /**
     * Shortcode for consent revoke link
     * Usage: [keks_revoke] or [keks_revoke text="Change cookie settings"]
     */
    public function shortcode_revoke_link($atts) {
        $atts = shortcode_atts([
            'text' => keks_t('shortcode_default_text'),
            'class' => '',
        ], $atts, 'keks_revoke');

        $classes = 'keks-revoke-link';
        if (!empty($atts['class'])) {
            $classes .= ' ' . esc_attr($atts['class']);
        }

        return '<a href="#" class="' . $classes . '">' . esc_html($atts['text']) . '</a>';
    }

    /**
     * Plugin-Aktivierung: Datenbank-Tabelle erstellen
     */
    public static function activate() {
        global $wpdb;
        $table = $wpdb->prefix . 'keks_consent_log';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            consent_id VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent VARCHAR(512),
            url VARCHAR(512),
            action VARCHAR(20) NOT NULL,
            categories TEXT NOT NULL,
            consent_version VARCHAR(32),
            INDEX idx_consent_id (consent_id),
            INDEX idx_created_at (created_at),
            INDEX idx_ip_address (ip_address)
        ) $charset;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('keks_db_version', '1.1');
    }

    /**
     * Datenbank-Upgrade prüfen und durchführen
     */
    public function maybe_upgrade_db() {
        $current_version = get_option('keks_db_version', '1.0');

        // Upgrade von 1.0 auf 1.1: ip_hash -> ip_address
        if (version_compare($current_version, '1.1', '<')) {
            global $wpdb;
            $table = $wpdb->prefix . 'keks_consent_log';

            // Prüfen ob Tabelle existiert
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
                // Prüfen ob alte Spalte ip_hash existiert
                $columns = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'ip_hash'");
                if (!empty($columns)) {
                    // Spalte umbenennen
                    $wpdb->query("ALTER TABLE $table CHANGE `ip_hash` `ip_address` VARCHAR(45) NOT NULL");
                }

                // Prüfen ob ip_address existiert, sonst hinzufügen
                $columns = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'ip_address'");
                if (empty($columns)) {
                    $wpdb->query("ALTER TABLE $table ADD `ip_address` VARCHAR(45) NOT NULL AFTER `created_at`");
                }
            }

            update_option('keks_db_version', '1.1');
        }
    }

    /**
     * AJAX-Handler für Consent-Logging
     */
    public function ajax_log_consent() {
        // Nonce prüfen
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'keks_consent_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce'], 403);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'keks_consent_log';

        // Prüfen ob Tabelle existiert
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
            wp_send_json_error(['message' => 'Table not found'], 500);
        }

        // Daten validieren
        $consent_id = sanitize_text_field($_POST['consent_id'] ?? '');
        $action = sanitize_key($_POST['consent_action'] ?? '');
        $categories = json_decode(stripslashes($_POST['categories'] ?? '{}'), true);
        $url = esc_url_raw($_POST['url'] ?? '');

        if (!in_array($action, ['accept_all', 'reject_all', 'custom', 'revoke'])) {
            wp_send_json_error(['message' => 'Invalid action'], 400);
        }

        // IP-Adresse speichern (für DSGVO-Nachweis)
        $raw_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ip_hash_only = get_option('keks_ip_hash_only', '0') === '1';
        $ip_address = $ip_hash_only ? hash('sha256', $raw_ip . wp_salt()) : $raw_ip;

        $wpdb->insert(
            $table,
            [
                'consent_id' => $consent_id ?: wp_generate_uuid4(),
                'created_at' => current_time('mysql'),
                'ip_address' => $ip_address,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
                'url' => $url,
                'action' => $action,
                'categories' => wp_json_encode($categories),
                'consent_version' => get_option('keks_consent_version', '1.0'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        wp_send_json_success(['consent_id' => $consent_id]);
    }

    /**
     * Prüft ob Banner-HTML gerendert werden soll
     * (Das Banner-HTML muss immer da sein, damit der Revoke-Link funktioniert)
     */
    public function should_render_banner() {
        // Plugin deaktiviert?
        if (get_option('keks_plugin_enabled', '1') !== '1') {
            return false;
        }

        // Im Admin-Bereich nie anzeigen
        if (is_admin()) {
            return false;
        }

        return true;
    }

    /**
     * Prüft ob die Seite blockiert werden soll (bis Consent gegeben wird)
     * Auf Datenschutz/Impressum/ausgeschlossenen Seiten wird nicht blockiert
     */
    public function should_block_page() {
        // Grundvoraussetzung: Banner muss überhaupt gerendert werden
        if (!$this->should_render_banner()) {
            return false;
        }

        // Auf Datenschutz-Seite nicht blockieren
        $privacy_page_id = get_option('keks_privacy_page_id', 0);
        if ($privacy_page_id && is_page($privacy_page_id)) {
            return false;
        }

        // Auf Impressum-Seite nicht blockieren
        $imprint_page_id = get_option('keks_imprint_page_id', 0);
        if ($imprint_page_id && is_page($imprint_page_id)) {
            return false;
        }

        // Weitere ausgeschlossene Seiten prüfen
        $excluded_pages = get_option('keks_excluded_pages', []);
        if (!empty($excluded_pages) && is_page($excluded_pages)) {
            return false;
        }

        // Individuelle Seiten-Ausnahme prüfen (Meta-Box)
        if (is_singular()) {
            $hide_banner = get_post_meta(get_the_ID(), '_keks_hide_banner', true);
            if ($hide_banner === '1') {
                return false;
            }
        }

        return true;
    }

    /**
     * Prüft ob Banner auf aktueller Seite angezeigt werden soll
     * @deprecated Use should_block_page() for blocking logic
     */
    public function should_show_banner() {
        return $this->should_block_page();
    }

    /**
     * Prüft ob eine Seite ausgeschlossen ist (für Meta-Box)
     */
    public function is_page_excluded($page_id) {
        $privacy_page_id = get_option('keks_privacy_page_id', 0);
        $imprint_page_id = get_option('keks_imprint_page_id', 0);
        $excluded_pages = get_option('keks_excluded_pages', []);

        if ($page_id == $privacy_page_id || $page_id == $imprint_page_id) {
            return true;
        }
        if (is_array($excluded_pages) && in_array($page_id, $excluded_pages)) {
            return true;
        }
        return false;
    }

    /**
     * Add meta box for pages/posts
     */
    public function add_meta_box() {
        $post_types = ['page', 'post'];

        foreach ($post_types as $post_type) {
            add_meta_box(
                'keks_meta_box',
                keks_t('metabox_title'),
                [$this, 'render_meta_box'],
                $post_type,
                'side',
                'low'
            );
        }
    }

    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('keks_meta_box', 'keks_meta_box_nonce');

        $hide_banner = get_post_meta($post->ID, '_keks_hide_banner', true);
        $is_excluded = $this->is_page_excluded($post->ID);
        ?>
        <label>
            <input type="checkbox" name="keks_hide_banner" value="1"
                   <?php checked($hide_banner, '1'); ?>
                   <?php disabled($is_excluded, true); ?>>
            <?php echo esc_html(keks_t('metabox_hide_banner')); ?>
        </label>
        <?php if ($is_excluded) : ?>
            <p class="description" style="margin-top: 8px; color: #666;">
                <em><?php echo esc_html(keks_t('metabox_excluded_note')); ?></em>
            </p>
        <?php endif; ?>
        <?php
    }

    /**
     * Meta-Box speichern
     */
    public function save_meta_box($post_id) {
        // Nonce prüfen
        if (!isset($_POST['keks_meta_box_nonce']) ||
            !wp_verify_nonce($_POST['keks_meta_box_nonce'], 'keks_meta_box')) {
            return;
        }

        // Autosave ignorieren
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Berechtigung prüfen
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Wert speichern
        $hide_banner = isset($_POST['keks_hide_banner']) ? '1' : '0';
        update_post_meta($post_id, '_keks_hide_banner', $hide_banner);
    }

    public function enqueue_assets() {
        // Plugin deaktiviert?
        if (get_option('keks_plugin_enabled', '1') !== '1') {
            return;
        }

        wp_enqueue_style(
            'keks-banner',
            KEKS_PLUGIN_URL . 'assets/css/banner.css',
            [],
            KEKS_VERSION
        );

        wp_enqueue_script(
            'keks-banner',
            KEKS_PLUGIN_URL . 'assets/js/banner.js',
            [],
            KEKS_VERSION,
            true
        );

        wp_localize_script('keks-banner', 'keksConfig', [
            'privacyUrl' => $this->get_privacy_url(),
            'consentDays' => 365,
            'requireConsent' => get_option('keks_require_consent', '1') === '1',
            'showBlockOverlay' => get_option('keks_show_block_overlay', '0') === '1',
            'requireConsentMessage' => (($msg = get_option('keks_require_consent_message', '')) !== '') ? $msg : $this->get_default_require_message(),
            'categories' => $this->get_categories(),
            'granularMode' => get_option('keks_granular_mode', '1') === '1',
            'shouldBlockPage' => $this->should_show_banner(),
            // AJAX for consent logging
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('keks_consent_nonce'),
            // Google Consent Mode v2
            'googleConsentMode' => get_option('keks_google_consent_mode', '0') === '1',
            // Translations for JavaScript
            'strings' => [
                'previewMode' => keks_t('js_preview_mode'),
                'acceptedPreview' => keks_t('js_accepted_preview'),
                'rejectedPreview' => keks_t('js_rejected_preview'),
                'selectionSavedPreview' => keks_t('js_selection_saved_preview'),
                'gcmUpdated' => keks_t('js_gcm_updated'),
                'localStorageUnavailable' => keks_t('js_localstorage_unavailable'),
                'loggingFailed' => keks_t('js_logging_failed'),
                'consentSaved' => keks_t('js_consent_saved'),
                'revokeFailed' => keks_t('js_revoke_failed'),
                'scriptActivated' => keks_t('js_script_activated'),
                'iframeActivated' => keks_t('js_iframe_activated'),
                'overlayMessage' => keks_t('js_overlay_message'),
                'overlayHint' => keks_t('js_overlay_hint'),
            ],
        ]);
    }

    private function get_default_require_message() {
        return keks_t('default_require_message');
    }

    public function render_banner() {
        // Banner-HTML immer rendern (versteckt), damit Revoke-Link funktioniert
        if (!$this->should_render_banner()) {
            return;
        }
        include KEKS_PLUGIN_DIR . 'templates/banner.php';
    }

    public function add_admin_menu() {
        // Top-Level Menu
        add_menu_page(
            keks_t('menu_main_title'),
            keks_t('menu_main'),
            'manage_options',
            'keks',
            [$this, 'render_settings_page'],
            'dashicons-shield-alt',
            81
        );

        // Subpages (first one overrides automatic menu entry)
        add_submenu_page('keks', keks_t('menu_settings'), keks_t('menu_settings'), 'manage_options', 'keks', [$this, 'render_settings_page']);
        add_submenu_page('keks', keks_t('menu_pages_title'), keks_t('menu_pages'), 'manage_options', 'keks-pages', [$this, 'render_pages_page']);
        add_submenu_page('keks', keks_t('menu_scripts'), keks_t('menu_scripts'), 'manage_options', 'keks-scripts', [$this, 'render_scripts_page']);
        add_submenu_page('keks', keks_t('menu_consent_log'), keks_t('menu_consent_log'), 'manage_options', 'keks-consent-log', [$this, 'render_consent_log_page']);
    }

    public function register_settings() {
        register_setting('keks_settings', 'keks_plugin_enabled');
        register_setting('keks_settings', 'keks_banner_text');

        // Seiten-Einstellungen (eigene Gruppe, um Datenverlust zu vermeiden)
        register_setting('keks_pages', 'keks_privacy_page_id');
        register_setting('keks_pages', 'keks_imprint_page_id');
        register_setting('keks_pages', 'keks_show_imprint_link');
        register_setting('keks_pages', 'keks_excluded_pages', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_excluded_pages'],
        ]);
        register_setting('keks_settings', 'keks_require_consent');
        register_setting('keks_settings', 'keks_show_block_overlay');
        register_setting('keks_settings', 'keks_require_consent_message');

        // Kategorien-Einstellungen
        register_setting('keks_settings', 'keks_granular_mode');
        register_setting('keks_settings', 'keks_enabled_categories', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_enabled_categories'],
        ]);

        // Kategorie-Namen und Beschreibungen
        foreach ($this->default_categories as $key => $category) {
            register_setting('keks_settings', "keks_category_{$key}_name");
            register_setting('keks_settings', "keks_category_{$key}_desc");
        }

        // Verwaltete Scripts (eigene Gruppe, um Datenverlust zu vermeiden)
        register_setting('keks_scripts', 'keks_managed_scripts', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize_managed_scripts'],
            'default' => [],
        ]);

        // Google Consent Mode v2
        register_setting('keks_settings', 'keks_google_consent_mode');

        // IP-Speicherung (Hash oder Klartext)
        register_setting('keks_settings', 'keks_ip_hash_only');
    }

    public function sanitize_enabled_categories($input) {
        if (!is_array($input)) {
            return ['necessary'];
        }
        // Necessary ist immer dabei
        if (!in_array('necessary', $input)) {
            $input[] = 'necessary';
        }
        return array_map('sanitize_key', $input);
    }

    public function sanitize_excluded_pages($input) {
        if (!is_array($input)) {
            return [];
        }
        return array_map('absint', $input);
    }

    public function sanitize_managed_scripts($input) {
        if (!is_array($input)) {
            return [];
        }

        $known_services = $this->get_known_services();
        $sanitized = [];

        foreach ($input as $script) {
            $service = sanitize_key($script['service'] ?? 'manual');

            // Bekannter Dienst
            if ($service !== 'manual' && isset($known_services[$service])) {
                $service_id = sanitize_text_field($script['service_id'] ?? '');
                $service_id2 = sanitize_text_field($script['service_id2'] ?? '');

                // Leere Einträge überspringen
                if (empty($service_id)) {
                    continue;
                }

                $entry = [
                    'service' => $service,
                    'service_id' => $service_id,
                ];

                // Zweite ID nur speichern wenn der Dienst sie benötigt
                if (isset($known_services[$service]['id2_label']) && !empty($service_id2)) {
                    $entry['service_id2'] = $service_id2;
                }

                $sanitized[] = $entry;
            } else {
                // Manuelles Script
                $type = isset($script['type']) && in_array($script['type'], ['url', 'inline'])
                    ? $script['type']
                    : 'url';

                // Content je nach Typ aus richtigem Feld holen
                $content = '';
                if ($type === 'url') {
                    $content = esc_url_raw($script['content'] ?? '');
                } else {
                    // Inline-Code aus content_inline Feld (oder content als Fallback)
                    $content = $script['content_inline'] ?? $script['content'] ?? '';
                }

                // Leere Einträge überspringen
                if (empty($script['name']) && empty($content)) {
                    continue;
                }

                $sanitized[] = [
                    'service' => 'manual',
                    'name' => sanitize_text_field($script['name'] ?? ''),
                    'type' => $type,
                    'content' => $content,
                    'category' => sanitize_key($script['category'] ?? 'statistics'),
                    'position' => isset($script['position']) && in_array($script['position'], ['head', 'footer'])
                        ? $script['position']
                        : 'footer',
                ];
            }
        }

        return $sanitized;
    }

    /**
     * Einstellungen-Seite rendern
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $banner_text_opt = get_option('keks_banner_text', '');
        $banner_text = !empty($banner_text_opt) ? $banner_text_opt : $this->get_default_banner_text();
        $require_consent = get_option('keks_require_consent', '1');
        $show_block_overlay = get_option('keks_show_block_overlay', '0');
        $require_consent_message_opt = get_option('keks_require_consent_message', '');
        $require_consent_message = !empty($require_consent_message_opt) ? $require_consent_message_opt : $this->get_default_require_message();
        $granular_mode = get_option('keks_granular_mode', '1');
        $enabled_categories = get_option('keks_enabled_categories', ['necessary', 'statistics', 'marketing']);
        if (!is_array($enabled_categories)) {
            $enabled_categories = ['necessary', 'statistics', 'marketing'];
        }
        ?>
        <?php $plugin_enabled = get_option('keks_plugin_enabled', '1'); ?>
        <div class="wrap">
            <h1><?php echo esc_html(keks_t('settings_page_title')); ?></h1>

            <?php settings_errors('keks_messages'); ?>

            <div class="notice notice-info" style="margin: 15px 0;">
                <p>
                    <strong><?php echo esc_html(keks_t('settings_tip')); ?></strong> <?php echo esc_html(keks_t('settings_tip_text')); ?>
                    <a href="<?php echo admin_url('admin.php?page=keks-scripts'); ?>"><?php echo esc_html(keks_t('menu_scripts')); ?></a> |
                    <a href="<?php echo admin_url('admin.php?page=keks-pages'); ?>"><?php echo esc_html(keks_t('menu_pages')); ?></a> |
                    <a href="<?php echo admin_url('admin.php?page=keks-consent-log'); ?>"><?php echo esc_html(keks_t('menu_consent_log')); ?></a>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields('keks_settings'); ?>

                <!-- Plugin Ein/Aus Schalter -->
                <div style="background: <?php echo $plugin_enabled === '1' ? '#d4edda' : '#f8d7da'; ?>; border: 1px solid <?php echo $plugin_enabled === '1' ? '#c3e6cb' : '#f5c6cb'; ?>; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 15px; cursor: pointer;">
                        <input type="checkbox" name="keks_plugin_enabled" value="1"
                               <?php checked($plugin_enabled, '1'); ?>
                               style="width: 20px; height: 20px;">
                        <span style="font-size: 16px; font-weight: 600;">
                            <?php echo esc_html(keks_t('settings_enable_banner')); ?>
                        </span>
                        <span style="font-size: 12px; padding: 4px 10px; border-radius: 4px; background: <?php echo $plugin_enabled === '1' ? '#28a745' : '#dc3545'; ?>; color: #fff;">
                            <?php echo $plugin_enabled === '1' ? esc_html(keks_t('settings_status_active')) : esc_html(keks_t('settings_status_inactive')); ?>
                        </span>
                    </label>
                    <p style="margin: 10px 0 0 35px; color: #666;">
                        <?php echo esc_html(keks_t('settings_enable_desc')); ?>
                    </p>
                </div>

                <h2 class="title"><?php echo esc_html(keks_t('settings_categories_title')); ?></h2>
                <p class="description"><?php echo esc_html(keks_t('settings_categories_desc')); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('settings_granular_mode')); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="keks_granular_mode" value="1"
                                       <?php checked($granular_mode, '1'); ?>>
                                <?php echo esc_html(keks_t('settings_granular_mode_label')); ?>
                            </label>
                            <p class="description">
                                <?php echo esc_html(keks_t('settings_granular_mode_desc')); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('settings_active_categories')); ?></th>
                        <td>
                            <?php foreach ($this->get_all_categories() as $key => $category) : ?>
                                <?php
                                $cat_name_opt = get_option("keks_category_{$key}_name", '');
                                $cat_desc_opt = get_option("keks_category_{$key}_desc", '');
                                // Fallback auf Default wenn leer
                                $cat_name = !empty($cat_name_opt) ? $cat_name_opt : $category['name'];
                                $cat_desc = !empty($cat_desc_opt) ? $cat_desc_opt : $category['description'];
                                ?>
                                <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-left: 4px solid <?php echo $category['required'] ? '#00a32a' : '#2271b1'; ?>;">
                                    <label style="display: block; margin-bottom: 10px;">
                                        <input type="checkbox"
                                               name="keks_enabled_categories[]"
                                               value="<?php echo esc_attr($key); ?>"
                                               <?php checked(in_array($key, $enabled_categories) || $category['required']); ?>
                                               <?php disabled($category['required'], true); ?>>
                                        <strong><?php echo esc_html($category['name']); ?></strong>
                                        <?php if ($category['required']) : ?>
                                            <span style="color: #00a32a; font-size: 12px;"><?php echo esc_html(keks_t('settings_always_active')); ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <div style="margin-left: 24px;">
                                        <label style="display: block; margin-bottom: 5px;">
                                            <span style="font-size: 12px; color: #666;"><?php echo esc_html(keks_t('settings_display_name')); ?></span><br>
                                            <input type="text"
                                                   name="keks_category_<?php echo esc_attr($key); ?>_name"
                                                   value="<?php echo esc_attr($cat_name); ?>"
                                                   class="large-text">
                                        </label>
                                        <label style="display: block;">
                                            <span style="font-size: 12px; color: #666;"><?php echo esc_html(keks_t('settings_description')); ?></span><br>
                                            <textarea name="keks_category_<?php echo esc_attr($key); ?>_desc"
                                                      rows="2"
                                                      class="large-text"><?php echo esc_textarea($cat_desc); ?></textarea>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php echo esc_html(keks_t('settings_banner_content')); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="keks_banner_text"><?php echo esc_html(keks_t('settings_banner_text')); ?></label>
                        </th>
                        <td>
                            <textarea name="keks_banner_text" id="keks_banner_text"
                                      rows="4" cols="60"><?php echo esc_textarea($banner_text); ?></textarea>
                            <p class="description"><?php echo esc_html(keks_t('settings_banner_text_desc')); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php echo esc_html(keks_t('settings_consent_required')); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('settings_block_mode')); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="keks_require_consent" value="1"
                                       <?php checked($require_consent, '1'); ?>>
                                <?php echo esc_html(keks_t('settings_block_mode_label')); ?>
                            </label>
                            <p class="description">
                                <?php echo esc_html(keks_t('settings_block_mode_desc')); ?>
                                <?php echo esc_html(keks_t('settings_block_mode_note')); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('settings_block_overlay')); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="keks_show_block_overlay" value="1"
                                       <?php checked($show_block_overlay, '1'); ?>>
                                <?php echo esc_html(keks_t('settings_block_overlay_label')); ?>
                            </label>
                            <p class="description">
                                <?php echo esc_html(keks_t('settings_block_overlay_desc')); ?>
                                <br><strong style="color: #d63638;"><?php echo esc_html(keks_t('settings_block_overlay_warning')); ?></strong> <?php echo esc_html(keks_t('settings_block_overlay_warning_text')); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="keks_require_consent_message"><?php echo esc_html(keks_t('settings_overlay_message')); ?></label>
                        </th>
                        <td>
                            <textarea name="keks_require_consent_message" id="keks_require_consent_message"
                                      rows="3" cols="60"><?php echo esc_textarea($require_consent_message); ?></textarea>
                            <p class="description"><?php echo esc_html(keks_t('settings_overlay_message_desc')); ?></p>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php echo esc_html(keks_t('settings_gcm_title')); ?></h2>
                <p class="description"><?php echo esc_html(keks_t('settings_gcm_desc')); ?></p>

                <?php $google_consent_mode = get_option('keks_google_consent_mode', '0'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('settings_gcm_enable')); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="keks_google_consent_mode" value="1"
                                       <?php checked($google_consent_mode, '1'); ?>>
                                <?php echo esc_html(keks_t('settings_gcm_enable_label')); ?>
                            </label>
                            <p class="description">
                                <?php echo esc_html(keks_t('settings_gcm_enable_desc')); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
                    <h4 style="margin: 0 0 10px;"><?php echo esc_html(keks_t('settings_gcm_how_title')); ?></h4>
                    <p style="margin: 0 0 10px;"><?php echo esc_html(keks_t('settings_gcm_how_text')); ?></p>
                    <table style="margin: 10px 0; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 5px 15px 5px 0; font-weight: bold;"><?php echo esc_html(keks_t('settings_gcm_keks_category')); ?></td>
                            <td style="padding: 5px 0;">→</td>
                            <td style="padding: 5px 0 5px 15px;"><?php echo esc_html(keks_t('settings_gcm_google_param')); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 15px 5px 0;"><code>statistics</code></td>
                            <td style="padding: 5px 0;">→</td>
                            <td style="padding: 5px 0 5px 15px;"><code>analytics_storage</code></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 15px 5px 0;"><code>marketing</code></td>
                            <td style="padding: 5px 0;">→</td>
                            <td style="padding: 5px 0 5px 15px;"><code>ad_storage</code>, <code>ad_user_data</code>, <code>ad_personalization</code></td>
                        </tr>
                    </table>
                    <p style="margin: 10px 0 0; font-size: 12px; color: #666;">
                        <?php echo keks_t('settings_gcm_auto_note'); ?>
                    </p>
                </div>

                <hr style="margin: 30px 0;">

                <h2 class="title"><?php echo esc_html(keks_t('settings_log_title')); ?></h2>
                <p class="description"><?php echo esc_html(keks_t('settings_log_desc')); ?></p>

                <?php $ip_hash_only = get_option('keks_ip_hash_only', '0'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('settings_ip_storage')); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="keks_ip_hash_only" value="1"
                                       <?php checked($ip_hash_only, '1'); ?>>
                                <?php echo esc_html(keks_t('settings_ip_hash_label')); ?>
                            </label>
                            <p class="description">
                                <strong><?php echo esc_html(keks_t('settings_ip_disabled')); ?></strong> <?php echo esc_html(keks_t('settings_ip_disabled_desc')); ?><br>
                                <strong><?php echo esc_html(keks_t('settings_ip_enabled')); ?></strong> <?php echo esc_html(keks_t('settings_ip_enabled_desc')); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <hr style="margin: 30px 0;">

            <h2 class="title"><?php echo esc_html(keks_t('settings_preview_title')); ?></h2>
            <p class="description"><?php echo esc_html(keks_t('settings_preview_desc')); ?></p>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html(keks_t('settings_preview_link')); ?></th>
                    <td>
                        <code style="background: #f0f0f0; padding: 8px 12px; display: inline-block; margin-bottom: 10px;">
                            <?php echo esc_url(home_url('/?keks_preview=1')); ?>
                        </code>
                        <br>
                        <a href="<?php echo esc_url(home_url('/?keks_preview=1')); ?>"
                           target="_blank"
                           class="button button-secondary">
                            <?php echo esc_html(keks_t('settings_preview_open')); ?>
                        </a>
                    </td>
                </tr>
            </table>

            <hr style="margin: 30px 0;">

            <h2 class="title"><?php echo esc_html(keks_t('settings_revoke_title')); ?></h2>
            <p class="description"><?php echo esc_html(keks_t('settings_revoke_desc')); ?></p>

            <div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; margin: 20px 0;">
                <h4 style="margin: 0 0 15px;"><?php echo esc_html(keks_t('settings_shortcode_title')); ?></h4>
                <p><?php echo esc_html(keks_t('settings_shortcode_desc')); ?></p>
                <table style="margin: 15px 0; border-collapse: collapse; width: 100%;">
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; font-family: monospace; border: 1px solid #ddd;">
                            [keks_revoke]
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            → <?php echo esc_html(keks_t('settings_shortcode_shows')); ?> "<?php echo esc_html(keks_t('shortcode_default_text')); ?>"
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; background: #f0f0f0; font-family: monospace; border: 1px solid #ddd;">
                            [keks_revoke text="Cookies verwalten"]
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            → <?php echo esc_html(keks_t('settings_shortcode_shows')); ?> "Cookies verwalten" <?php echo esc_html(keks_t('settings_shortcode_custom')); ?>
                        </td>
                    </tr>
                </table>

                <h4 style="margin: 20px 0 15px;"><?php echo esc_html(keks_t('settings_html_title')); ?></h4>
                <p><?php echo esc_html(keks_t('settings_html_desc')); ?></p>
                <code style="display: block; background: #f0f0f0; padding: 10px; margin: 10px 0; font-size: 13px;">
                    &lt;a href="#" class="keks-revoke-link"&gt;<?php echo esc_html(keks_t('shortcode_default_text')); ?>&lt;/a&gt;
                </code>

                <h4 style="margin: 20px 0 15px;"><?php echo esc_html(keks_t('settings_placements_title')); ?></h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><strong><?php echo esc_html(keks_t('settings_placement_footer')); ?></strong> – <?php echo esc_html(keks_t('settings_placement_footer_desc')); ?></li>
                    <li><strong><?php echo esc_html(keks_t('settings_placement_privacy')); ?></strong> – <?php echo esc_html(keks_t('settings_placement_privacy_desc')); ?></li>
                    <li><strong><?php echo esc_html(keks_t('settings_placement_cookie')); ?></strong> – <?php echo esc_html(keks_t('settings_placement_cookie_desc')); ?></li>
                </ul>
            </div>

                <?php submit_button(keks_t('settings_save')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Scripts-Seite rendern
     */
    public function render_scripts_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $managed_scripts = get_option('keks_managed_scripts', []);
        if (!is_array($managed_scripts)) {
            $managed_scripts = [];
        }
        $known_services = $this->get_known_services();
        $available_categories = $this->get_categories();

        // Dienste nach Kategorie gruppieren
        $services_by_category = [
            'statistics' => [],
            'marketing' => [],
            'necessary' => [],
        ];
        foreach ($known_services as $key => $service) {
            $cat = $service['category'];
            if (!isset($services_by_category[$cat])) {
                $services_by_category[$cat] = [];
            }
            $services_by_category[$cat][$key] = $service;
        }
        $category_labels = [
            'statistics' => keks_t('scripts_category_statistics'),
            'marketing' => keks_t('scripts_category_marketing'),
            'necessary' => keks_t('scripts_category_necessary'),
        ];
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(keks_t('scripts_page_title')); ?></h1>

            <?php settings_errors('keks_messages'); ?>

            <p class="description"><?php echo esc_html(keks_t('scripts_page_desc')); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields('keks_scripts'); ?>

            <div style="margin: 20px 0;">
                <label for="keks-service-select"><strong><?php echo esc_html(keks_t('scripts_add_service')); ?></strong></label>
                <select id="keks-service-select" style="min-width: 300px; margin-left: 10px;">
                    <option value=""><?php echo esc_html(keks_t('scripts_select_service')); ?></option>
                    <?php foreach ($services_by_category as $cat_key => $services) : ?>
                        <?php if (!empty($services)) : ?>
                            <optgroup label="<?php echo esc_attr($category_labels[$cat_key] ?? ucfirst($cat_key)); ?>">
                                <?php foreach ($services as $key => $service) : ?>
                                    <option value="<?php echo esc_attr($key); ?>">
                                        <?php echo esc_html($service['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <optgroup label="<?php echo esc_attr(keks_t('scripts_category_other')); ?>">
                        <option value="manual"><?php echo esc_html(keks_t('scripts_manual_entry')); ?></option>
                    </optgroup>
                </select>
                <button type="button" class="button button-primary" id="keks-add-service" style="margin-left: 5px;"><?php echo esc_html(keks_t('scripts_add_button')); ?></button>
            </div>

            <div id="keks-scripts-manager" style="margin-top: 20px;">
                <?php foreach ($managed_scripts as $index => $script) : ?>
                    <?php
                    $service_key = $script['service'] ?? 'manual';
                    $is_known = isset($known_services[$service_key]);
                    $service_info = $is_known ? $known_services[$service_key] : null;
                    $category_name = $is_known ? ($category_labels[$service_info['category']] ?? keks_t('scripts_category_other')) : ($available_categories[$script['category']]['name'] ?? keks_t('scripts_category_statistics'));
                    $border_color = ($is_known && $service_info['category'] === 'marketing') ? '#d63638' : '#2271b1';
                    ?>
                    <div class="keks-script-item" data-index="<?php echo esc_attr($index); ?>" data-service="<?php echo esc_attr($service_key); ?>" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-left: 4px solid <?php echo $border_color; ?>;">
                        <input type="hidden" name="keks_managed_scripts[<?php echo esc_attr($index); ?>][service]" value="<?php echo esc_attr($service_key); ?>">

                        <?php if ($is_known) : ?>
                            <!-- Bekannter Dienst -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div>
                                    <strong style="font-size: 14px;"><?php echo esc_html($service_info['name']); ?></strong>
                                    <span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 3px; margin-left: 8px; background: <?php echo $service_info['category'] === 'marketing' ? '#fce4e4' : '#e7f3ff'; ?>; color: <?php echo $service_info['category'] === 'marketing' ? '#8b0000' : '#0066cc'; ?>;">
                                        <?php echo esc_html($category_name); ?>
                                    </span>
                                    <span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 3px; margin-left: 4px; background: #f0f0f0; color: #666;">
                                        <?php echo $service_info['position'] === 'head' ? esc_html(keks_t('scripts_position_head')) : esc_html(keks_t('scripts_position_footer')); ?>
                                    </span>
                                </div>
                                <button type="button" class="button keks-remove-script" style="color: #b32d2e;"><?php echo esc_html(keks_t('scripts_remove_button')); ?></button>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-size: 13px; color: #666;">
                                    <?php echo esc_html($service_info['id_label']); ?>:
                                </label>
                                <input type="text"
                                       name="keks_managed_scripts[<?php echo esc_attr($index); ?>][service_id]"
                                       value="<?php echo esc_attr($script['service_id'] ?? ''); ?>"
                                       placeholder="<?php echo esc_attr($service_info['id_placeholder']); ?>"
                                       style="width: 100%; max-width: 400px;">
                                <?php if (isset($service_info['id2_label'])) : ?>
                                    <label style="display: block; margin: 10px 0 5px; font-size: 13px; color: #666;">
                                        <?php echo esc_html($service_info['id2_label']); ?>:
                                    </label>
                                    <input type="text"
                                           name="keks_managed_scripts[<?php echo esc_attr($index); ?>][service_id2]"
                                           value="<?php echo esc_attr($script['service_id2'] ?? ''); ?>"
                                           placeholder="<?php echo esc_attr($service_info['id2_placeholder']); ?>"
                                           style="width: 100%; max-width: 200px;">
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <!-- Manuelles Script -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div>
                                    <strong style="font-size: 14px;"><?php echo esc_html(keks_t('scripts_manual_script')); ?></strong>
                                    <span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 3px; margin-left: 8px; background: #f0f0f0; color: #666;"><?php echo esc_html(keks_t('scripts_custom')); ?></span>
                                </div>
                                <button type="button" class="button keks-remove-script" style="color: #b32d2e;"><?php echo esc_html(keks_t('scripts_remove_button')); ?></button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 120px; gap: 10px; margin-bottom: 10px;">
                                <input type="text"
                                       name="keks_managed_scripts[<?php echo esc_attr($index); ?>][name]"
                                       value="<?php echo esc_attr($script['name'] ?? ''); ?>"
                                       placeholder="<?php echo esc_attr(keks_t('scripts_name_placeholder')); ?>"
                                       style="width: 100%;">
                                <select name="keks_managed_scripts[<?php echo esc_attr($index); ?>][type]" class="keks-script-type">
                                    <option value="url" <?php selected($script['type'] ?? 'url', 'url'); ?>><?php echo esc_html(keks_t('scripts_type_url')); ?></option>
                                    <option value="inline" <?php selected($script['type'] ?? 'url', 'inline'); ?>><?php echo esc_html(keks_t('scripts_type_inline')); ?></option>
                                </select>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <input type="text"
                                       name="keks_managed_scripts[<?php echo esc_attr($index); ?>][content]"
                                       value="<?php echo ($script['type'] ?? 'url') === 'url' ? esc_attr($script['content'] ?? '') : ''; ?>"
                                       placeholder="<?php echo esc_attr(keks_t('scripts_url_placeholder')); ?>"
                                       class="keks-script-content"
                                       style="width: 100%; <?php echo ($script['type'] ?? 'url') === 'inline' ? 'display: none;' : ''; ?>">
                                <textarea name="keks_managed_scripts[<?php echo esc_attr($index); ?>][content_inline]"
                                          placeholder="<?php echo esc_attr(keks_t('scripts_code_placeholder')); ?>"
                                          class="keks-script-content-inline"
                                          rows="3"
                                          style="width: 100%; margin-top: 5px; <?php echo ($script['type'] ?? 'url') === 'url' ? 'display: none;' : ''; ?>"><?php echo ($script['type'] ?? 'url') === 'inline' ? esc_textarea($script['content'] ?? '') : ''; ?></textarea>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <select name="keks_managed_scripts[<?php echo esc_attr($index); ?>][category]" style="min-width: 120px;">
                                    <?php foreach ($available_categories as $key => $cat) : ?>
                                        <?php if (!$cat['required']) : ?>
                                            <option value="<?php echo esc_attr($key); ?>" <?php selected($script['category'] ?? 'statistics', $key); ?>><?php echo esc_html($cat['name']); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <select name="keks_managed_scripts[<?php echo esc_attr($index); ?>][position]" style="min-width: 100px;">
                                    <option value="head" <?php selected($script['position'] ?? 'footer', 'head'); ?>><?php echo esc_html(keks_t('scripts_position_in_head')); ?></option>
                                    <option value="footer" <?php selected($script['position'] ?? 'footer', 'footer'); ?>><?php echo esc_html(keks_t('scripts_position_in_footer')); ?></option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($managed_scripts)) : ?>
                <p style="color: #666; font-style: italic;"><?php echo esc_html(keks_t('scripts_none_added')); ?></p>
            <?php endif; ?>

            <script>
            (function() {
                var manager = document.getElementById('keks-scripts-manager');
                var serviceSelect = document.getElementById('keks-service-select');
                var addBtn = document.getElementById('keks-add-service');
                var nextIndex = <?php echo max(count($managed_scripts), 0); ?>;
                var knownServices = <?php echo json_encode($known_services); ?>;
                var categoryLabels = <?php echo json_encode($category_labels); ?>;
                var availableCategories = <?php echo json_encode(array_filter($available_categories, function($cat) { return !$cat['required']; })); ?>;

                // Translations
                var i18n = {
                    selectAlert: <?php echo json_encode(keks_t('scripts_select_alert')); ?>,
                    categoryOther: <?php echo json_encode(keks_t('scripts_category_other')); ?>,
                    positionHead: <?php echo json_encode(keks_t('scripts_position_head')); ?>,
                    positionFooter: <?php echo json_encode(keks_t('scripts_position_footer')); ?>,
                    removeButton: <?php echo json_encode(keks_t('scripts_remove_button')); ?>,
                    manualScript: <?php echo json_encode(keks_t('scripts_manual_script')); ?>,
                    custom: <?php echo json_encode(keks_t('scripts_custom')); ?>,
                    namePlaceholder: <?php echo json_encode(keks_t('scripts_name_placeholder')); ?>,
                    typeUrl: <?php echo json_encode(keks_t('scripts_type_url')); ?>,
                    typeInline: <?php echo json_encode(keks_t('scripts_type_inline')); ?>,
                    urlPlaceholder: <?php echo json_encode(keks_t('scripts_url_placeholder')); ?>,
                    codePlaceholder: <?php echo json_encode(keks_t('scripts_code_placeholder')); ?>,
                    positionInHead: <?php echo json_encode(keks_t('scripts_position_in_head')); ?>,
                    positionInFooter: <?php echo json_encode(keks_t('scripts_position_in_footer')); ?>
                };

                // Dienst hinzufügen
                addBtn.addEventListener('click', function() {
                    var serviceKey = serviceSelect.value;
                    if (!serviceKey) {
                        alert(i18n.selectAlert);
                        return;
                    }

                    var html = '';
                    if (serviceKey === 'manual') {
                        html = createManualScriptHtml(nextIndex);
                    } else {
                        var service = knownServices[serviceKey];
                        if (!service) return;
                        html = createKnownServiceHtml(nextIndex, serviceKey, service);
                    }

                    // Platzhalter-Text entfernen falls vorhanden
                    var placeholder = manager.parentNode.querySelector('p[style*="italic"]');
                    if (placeholder) placeholder.remove();

                    manager.insertAdjacentHTML('beforeend', html);
                    nextIndex++;
                    serviceSelect.value = '';
                });

                function createKnownServiceHtml(index, key, service) {
                    var catLabel = categoryLabels[service.category] || i18n.categoryOther;
                    var bgColor = service.category === 'marketing' ? '#fce4e4' : '#e7f3ff';
                    var textColor = service.category === 'marketing' ? '#8b0000' : '#0066cc';
                    var borderColor = service.category === 'marketing' ? '#d63638' : '#2271b1';

                    var id2Html = '';
                    if (service.id2_label) {
                        id2Html = '<label style="display: block; margin: 10px 0 5px; font-size: 13px; color: #666;">' + service.id2_label + ':</label>' +
                                  '<input type="text" name="keks_managed_scripts[' + index + '][service_id2]" placeholder="' + (service.id2_placeholder || '') + '" style="width: 100%; max-width: 200px;">';
                    }

                    return '<div class="keks-script-item" data-index="' + index + '" data-service="' + key + '" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-left: 4px solid ' + borderColor + ';">' +
                        '<input type="hidden" name="keks_managed_scripts[' + index + '][service]" value="' + key + '">' +
                        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">' +
                            '<div>' +
                                '<strong style="font-size: 14px;">' + service.name + '</strong>' +
                                '<span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 3px; margin-left: 8px; background: ' + bgColor + '; color: ' + textColor + ';">' + catLabel + '</span>' +
                                '<span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 3px; margin-left: 4px; background: #f0f0f0; color: #666;">' + (service.position === 'head' ? i18n.positionHead : i18n.positionFooter) + '</span>' +
                            '</div>' +
                            '<button type="button" class="button keks-remove-script" style="color: #b32d2e;">' + i18n.removeButton + '</button>' +
                        '</div>' +
                        '<div>' +
                            '<label style="display: block; margin-bottom: 5px; font-size: 13px; color: #666;">' + service.id_label + ':</label>' +
                            '<input type="text" name="keks_managed_scripts[' + index + '][service_id]" placeholder="' + service.id_placeholder + '" style="width: 100%; max-width: 400px;">' +
                            id2Html +
                        '</div>' +
                    '</div>';
                }

                function createManualScriptHtml(index) {
                    var categoryOptions = '';
                    for (var key in availableCategories) {
                        categoryOptions += '<option value="' + key + '">' + availableCategories[key].name + '</option>';
                    }

                    return '<div class="keks-script-item" data-index="' + index + '" data-service="manual" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-left: 4px solid #2271b1;">' +
                        '<input type="hidden" name="keks_managed_scripts[' + index + '][service]" value="manual">' +
                        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">' +
                            '<div>' +
                                '<strong style="font-size: 14px;">' + i18n.manualScript + '</strong>' +
                                '<span style="display: inline-block; font-size: 11px; padding: 2px 8px; border-radius: 3px; margin-left: 8px; background: #f0f0f0; color: #666;">' + i18n.custom + '</span>' +
                            '</div>' +
                            '<button type="button" class="button keks-remove-script" style="color: #b32d2e;">' + i18n.removeButton + '</button>' +
                        '</div>' +
                        '<div style="display: grid; grid-template-columns: 1fr 120px; gap: 10px; margin-bottom: 10px;">' +
                            '<input type="text" name="keks_managed_scripts[' + index + '][name]" placeholder="' + i18n.namePlaceholder + '" style="width: 100%;">' +
                            '<select name="keks_managed_scripts[' + index + '][type]" class="keks-script-type">' +
                                '<option value="url">' + i18n.typeUrl + '</option>' +
                                '<option value="inline">' + i18n.typeInline + '</option>' +
                            '</select>' +
                        '</div>' +
                        '<div style="margin-bottom: 10px;">' +
                            '<input type="text" name="keks_managed_scripts[' + index + '][content]" placeholder="' + i18n.urlPlaceholder + '" class="keks-script-content" style="width: 100%;">' +
                            '<textarea name="keks_managed_scripts[' + index + '][content_inline]" placeholder="' + i18n.codePlaceholder + '" class="keks-script-content-inline" rows="3" style="width: 100%; display: none; margin-top: 5px;"></textarea>' +
                        '</div>' +
                        '<div style="display: flex; gap: 10px; align-items: center;">' +
                            '<select name="keks_managed_scripts[' + index + '][category]" style="min-width: 120px;">' + categoryOptions + '</select>' +
                            '<select name="keks_managed_scripts[' + index + '][position]" style="min-width: 100px;">' +
                                '<option value="head">' + i18n.positionInHead + '</option>' +
                                '<option value="footer" selected>' + i18n.positionInFooter + '</option>' +
                            '</select>' +
                        '</div>' +
                    '</div>';
                }

                // Script entfernen
                manager.addEventListener('click', function(e) {
                    if (e.target.classList.contains('keks-remove-script')) {
                        e.target.closest('.keks-script-item').remove();
                    }
                });

                // Typ wechseln (URL <-> Inline) für manuelle Scripts
                manager.addEventListener('change', function(e) {
                    if (e.target.classList.contains('keks-script-type')) {
                        var item = e.target.closest('.keks-script-item');
                        var urlInput = item.querySelector('.keks-script-content');
                        var inlineTextarea = item.querySelector('.keks-script-content-inline');

                        if (e.target.value === 'inline') {
                            urlInput.style.display = 'none';
                            inlineTextarea.style.display = 'block';
                        } else {
                            urlInput.style.display = 'block';
                            inlineTextarea.style.display = 'none';
                        }
                    }
                });
            })();
            </script>

            <?php submit_button(keks_t('settings_save')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Seiten-Ausnahmen rendern
     */
    public function render_pages_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $pages = get_pages();
        $privacy_page_id = get_option('keks_privacy_page_id', 0);
        $imprint_page_id = get_option('keks_imprint_page_id', 0);
        $show_imprint_link = get_option('keks_show_imprint_link', '1');
        $excluded_pages = get_option('keks_excluded_pages', []);
        if (!is_array($excluded_pages)) {
            $excluded_pages = [];
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(keks_t('pages_page_title')); ?></h1>

            <?php settings_errors('keks_messages'); ?>

            <p class="description"><?php echo esc_html(keks_t('pages_page_desc')); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields('keks_pages'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="keks_privacy_page_id"><?php echo esc_html(keks_t('pages_privacy_page')); ?></label>
                        </th>
                        <td>
                            <select name="keks_privacy_page_id" id="keks_privacy_page_id">
                                <option value="0"><?php echo esc_html(keks_t('pages_please_select')); ?></option>
                                <?php foreach ($pages as $page) : ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>"
                                            <?php selected($privacy_page_id, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php echo esc_html(keks_t('pages_privacy_desc')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="keks_imprint_page_id"><?php echo esc_html(keks_t('pages_imprint_page')); ?></label>
                        </th>
                        <td>
                            <select name="keks_imprint_page_id" id="keks_imprint_page_id">
                                <option value="0"><?php echo esc_html(keks_t('pages_please_select')); ?></option>
                                <?php foreach ($pages as $page) : ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>"
                                            <?php selected($imprint_page_id, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php echo esc_html(keks_t('pages_imprint_desc')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html(keks_t('pages_imprint_link')); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="keks_show_imprint_link" value="1"
                                       <?php checked($show_imprint_link, '1'); ?>>
                                <?php echo esc_html(keks_t('pages_imprint_link_label')); ?>
                            </label>
                            <p class="description"><?php echo esc_html(keks_t('pages_imprint_link_desc')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php echo esc_html(keks_t('pages_more_exceptions')); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php echo esc_html(keks_t('pages_more_exceptions')); ?></legend>
                                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                                    <?php foreach ($pages as $page) : ?>
                                        <?php
                                        // Datenschutz und Impressum nicht in der Liste anzeigen
                                        if ($page->ID == $privacy_page_id || $page->ID == $imprint_page_id) {
                                            continue;
                                        }
                                        ?>
                                        <label style="display: block; margin-bottom: 5px;">
                                            <input type="checkbox"
                                                   name="keks_excluded_pages[]"
                                                   value="<?php echo esc_attr($page->ID); ?>"
                                                   <?php checked(in_array($page->ID, $excluded_pages)); ?>>
                                            <?php echo esc_html($page->post_title); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description"><?php echo esc_html(keks_t('pages_more_exceptions_desc')); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <div class="notice notice-info" style="margin: 20px 0;">
                    <p><strong><?php echo esc_html(keks_t('settings_tip')); ?></strong> <?php echo esc_html(keks_t('pages_tip')); ?></p>
                </div>

                <?php submit_button(keks_t('settings_save')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Consent-Log Seite rendern
     */
    public function render_consent_log_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'keks_consent_log';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
        $ip_hash_only = get_option('keks_ip_hash_only', '0') === '1';
        $ip_column_label = $ip_hash_only ? keks_t('log_column_ip_hash') : keks_t('log_column_ip_address');

        // Pagination
        $per_page = 50;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        // Filter
        $filter_action = isset($_GET['filter_action']) ? sanitize_key($_GET['filter_action']) : '';
        $filter_date = isset($_GET['filter_date']) ? sanitize_text_field($_GET['filter_date']) : '';

        // Daten laden
        $entries = [];
        $total = 0;

        if ($table_exists) {
            $where = "1=1";
            $where_args = [];

            if ($filter_action) {
                $where .= " AND action = %s";
                $where_args[] = $filter_action;
            }

            if ($filter_date) {
                $where .= " AND DATE(created_at) = %s";
                $where_args[] = $filter_date;
            }

            $count_sql = "SELECT COUNT(*) FROM $table WHERE $where";
            if (!empty($where_args)) {
                $total = (int) $wpdb->get_var($wpdb->prepare($count_sql, $where_args));
            } else {
                $total = (int) $wpdb->get_var($count_sql);
            }

            $sql = "SELECT * FROM $table WHERE $where ORDER BY created_at DESC LIMIT %d OFFSET %d";
            $args = array_merge($where_args, [$per_page, $offset]);
            $entries = $wpdb->get_results($wpdb->prepare($sql, $args));
        }

        $total_pages = ceil($total / $per_page);

        // CSV Export
        if (isset($_GET['export']) && $_GET['export'] === 'csv' && $table_exists) {
            $this->export_consent_log_csv();
            return;
        }

        // Löschen alter Einträge
        if (isset($_POST['delete_old']) && wp_verify_nonce($_POST['_wpnonce'], 'keks_delete_old_logs')) {
            $days = intval($_POST['delete_days']);
            if ($days > 0 && $table_exists) {
                $deleted = $wpdb->query($wpdb->prepare(
                    "DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                    $days
                ));
                echo '<div class="notice notice-success"><p>' . sprintf(keks_t('log_entries_deleted'), $deleted) . '</p></div>';
            }
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(keks_t('log_page_title')); ?></h1>

            <?php if (!$table_exists) : ?>
                <div class="notice notice-error">
                    <p><strong><?php echo esc_html(keks_t('log_table_not_found')); ?></strong> <?php echo esc_html(keks_t('log_table_recreate')); ?></p>
                </div>
            <?php else : ?>

                <div style="display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap;">
                    <form method="get" action="" style="display: flex; gap: 10px; align-items: center;">
                        <input type="hidden" name="page" value="keks-consent-log">
                        <select name="filter_action">
                            <option value=""><?php echo esc_html(keks_t('log_filter_all_actions')); ?></option>
                            <option value="accept_all" <?php selected($filter_action, 'accept_all'); ?>><?php echo esc_html(keks_t('log_filter_accept_all')); ?></option>
                            <option value="reject_all" <?php selected($filter_action, 'reject_all'); ?>><?php echo esc_html(keks_t('log_filter_reject_all')); ?></option>
                            <option value="custom" <?php selected($filter_action, 'custom'); ?>><?php echo esc_html(keks_t('log_filter_custom')); ?></option>
                            <option value="revoke" <?php selected($filter_action, 'revoke'); ?>><?php echo esc_html(keks_t('log_filter_revoke')); ?></option>
                        </select>
                        <input type="date" name="filter_date" value="<?php echo esc_attr($filter_date); ?>">
                        <button type="submit" class="button"><?php echo esc_html(keks_t('log_filter_button')); ?></button>
                        <?php if ($filter_action || $filter_date) : ?>
                            <a href="<?php echo admin_url('admin.php?page=keks-consent-log'); ?>" class="button"><?php echo esc_html(keks_t('log_reset_button')); ?></a>
                        <?php endif; ?>
                    </form>

                    <div style="margin-left: auto; display: flex; gap: 10px;">
                        <a href="<?php echo admin_url('admin.php?page=keks-consent-log&export=csv'); ?>" class="button"><?php echo esc_html(keks_t('log_export_csv')); ?></a>
                    </div>
                </div>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php echo esc_html(keks_t('log_column_id')); ?></th>
                            <th style="width: 150px;"><?php echo esc_html(keks_t('log_column_date')); ?></th>
                            <th style="width: 120px;"><?php echo esc_html(keks_t('log_column_action')); ?></th>
                            <th><?php echo esc_html(keks_t('log_column_categories')); ?></th>
                            <th style="width: 120px;"><?php echo esc_html($ip_column_label); ?></th>
                            <th style="width: 180px;"><?php echo esc_html(keks_t('log_column_page')); ?></th>
                            <th style="width: 100px;"><?php echo esc_html(keks_t('log_column_browser')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($entries)) : ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;"><?php echo esc_html(keks_t('log_no_entries')); ?></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($entries as $entry) : ?>
                                <?php
                                $categories = json_decode($entry->categories, true);
                                $cat_labels = [];
                                if (is_array($categories)) {
                                    foreach ($categories as $cat => $enabled) {
                                        if ($enabled) {
                                            $cat_labels[] = '<span style="background:#e7f3ff;padding:2px 6px;border-radius:3px;font-size:11px;">' . esc_html(ucfirst($cat)) . '</span>';
                                        }
                                    }
                                }
                                $browser = '';
                                if (!empty($entry->user_agent)) {
                                    if (strpos($entry->user_agent, 'Chrome') !== false) $browser = 'Chrome';
                                    elseif (strpos($entry->user_agent, 'Firefox') !== false) $browser = 'Firefox';
                                    elseif (strpos($entry->user_agent, 'Safari') !== false) $browser = 'Safari';
                                    elseif (strpos($entry->user_agent, 'Edge') !== false) $browser = 'Edge';
                                    else $browser = keks_t('log_browser_other');
                                }
                                ?>
                                <tr>
                                    <td><?php echo esc_html($entry->id); ?></td>
                                    <td><?php echo esc_html(date_i18n('d.m.Y H:i:s', strtotime($entry->created_at))); ?></td>
                                    <td>
                                        <?php
                                        $action_labels = [
                                            'accept_all' => '<span style="color:#00a32a;">' . esc_html(keks_t('log_action_accept')) . '</span>',
                                            'reject_all' => '<span style="color:#d63638;">' . esc_html(keks_t('log_action_reject')) . '</span>',
                                            'custom' => '<span style="color:#dba617;">' . esc_html(keks_t('log_action_custom')) . '</span>',
                                            'revoke' => '<span style="color:#666;">' . esc_html(keks_t('log_action_revoke')) . '</span>',
                                        ];
                                        echo $action_labels[$entry->action] ?? esc_html($entry->action);
                                        ?>
                                    </td>
                                    <td><?php echo implode(' ', $cat_labels); ?></td>
                                    <td><?php echo esc_html($entry->ip_address ?? '-'); ?></td>
                                    <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo esc_attr($entry->url); ?>">
                                        <?php echo esc_html($entry->url ?: '-'); ?>
                                    </td>
                                    <td><?php echo esc_html($browser); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1) : ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php echo number_format_i18n($total); ?> <?php echo esc_html(keks_t('log_entries')); ?></span>
                            <span class="pagination-links">
                                <?php
                                $base_url = admin_url('admin.php?page=keks-consent-log');
                                if ($filter_action) $base_url .= '&filter_action=' . urlencode($filter_action);
                                if ($filter_date) $base_url .= '&filter_date=' . urlencode($filter_date);

                                if ($current_page > 1) : ?>
                                    <a class="prev-page button" href="<?php echo esc_url($base_url . '&paged=' . ($current_page - 1)); ?>">‹</a>
                                <?php else : ?>
                                    <span class="tablenav-pages-navspan button disabled">‹</span>
                                <?php endif; ?>

                                <span class="paging-input">
                                    <span class="current-page"><?php echo $current_page; ?></span>
                                    <?php echo esc_html(keks_t('log_of')); ?>
                                    <span class="total-pages"><?php echo $total_pages; ?></span>
                                </span>

                                <?php if ($current_page < $total_pages) : ?>
                                    <a class="next-page button" href="<?php echo esc_url($base_url . '&paged=' . ($current_page + 1)); ?>">›</a>
                                <?php else : ?>
                                    <span class="tablenav-pages-navspan button disabled">›</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <hr style="margin: 30px 0;">

                <h2><?php echo esc_html(keks_t('log_manage_data')); ?></h2>
                <form method="post" style="display: flex; gap: 10px; align-items: center;">
                    <?php wp_nonce_field('keks_delete_old_logs'); ?>
                    <label><?php echo esc_html(keks_t('log_delete_older')); ?></label>
                    <input type="number" name="delete_days" value="365" min="1" max="3650" style="width: 80px;"> <?php echo esc_html(keks_t('log_days')); ?>
                    <button type="submit" name="delete_old" class="button" onclick="return confirm('<?php echo esc_js(keks_t('log_delete_confirm')); ?>');"><?php echo esc_html(keks_t('log_delete_old_button')); ?></button>
                </form>
                <p class="description" style="margin-top: 10px;"><?php echo esc_html(keks_t('log_gdpr_note')); ?></p>

            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * CSV Export für Consent-Log
     */
    private function export_consent_log_csv() {
        global $wpdb;
        $table = $wpdb->prefix . 'keks_consent_log';
        $ip_hash_only = get_option('keks_ip_hash_only', '0') === '1';
        $ip_column_label = $ip_hash_only ? keks_t('log_column_ip_hash') : keks_t('log_column_ip_address');

        $entries = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=keks-consent-log-' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');

        // BOM für Excel UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header
        fputcsv($output, [keks_t('log_column_id'), keks_t('csv_consent_id'), keks_t('csv_date'), keks_t('csv_action'), keks_t('csv_categories'), keks_t('csv_url'), $ip_column_label, keks_t('csv_version')], ';');

        foreach ($entries as $entry) {
            fputcsv($output, [
                $entry->id,
                $entry->consent_id,
                $entry->created_at,
                $entry->action,
                $entry->categories,
                $entry->url,
                $entry->ip_address,
                $entry->consent_version,
            ], ';');
        }

        fclose($output);
        exit;
    }

    public function get_privacy_url() {
        $page_id = get_option('keks_privacy_page_id', 0);
        if ($page_id) {
            return get_permalink($page_id);
        }
        return '';
    }

    public function get_imprint_url() {
        $page_id = get_option('keks_imprint_page_id', 0);
        if ($page_id) {
            return get_permalink($page_id);
        }
        return '';
    }

    public function show_imprint_link() {
        return get_option('keks_show_imprint_link', '1') === '1';
    }

    public function get_banner_text() {
        $text = get_option('keks_banner_text', '');
        return !empty($text) ? $text : $this->get_default_banner_text();
    }

    private function get_default_banner_text() {
        return keks_t('default_banner_text');
    }

    /**
     * Returns enabled categories
     */
    public function get_categories() {
        $enabled = get_option('keks_enabled_categories', ['necessary', 'statistics', 'marketing']);
        if (!is_array($enabled)) {
            $enabled = ['necessary', 'statistics', 'marketing'];
        }

        $categories = [];
        foreach ($this->default_categories as $key => $category) {
            if (in_array($key, $enabled) || $category['required']) {
                $cat_name_opt = get_option("keks_category_{$key}_name", '');
                $cat_desc_opt = get_option("keks_category_{$key}_desc", '');
                $categories[$key] = [
                    'name' => !empty($cat_name_opt) ? $cat_name_opt : keks_t($category['name_key']),
                    'description' => !empty($cat_desc_opt) ? $cat_desc_opt : keks_t($category['desc_key']),
                    'required' => $category['required'],
                ];
            }
        }

        return $categories;
    }

    /**
     * Returns all available categories (for admin)
     */
    public function get_all_categories() {
        $categories = [];
        foreach ($this->default_categories as $key => $category) {
            $categories[$key] = [
                'name' => keks_t($category['name_key']),
                'description' => keks_t($category['desc_key']),
                'required' => $category['required'],
            ];
        }
        return $categories;
    }

    /**
     * Google Consent Mode v2 Default-Zustand ausgeben
     */
    public function render_google_consent_mode() {
        // Plugin deaktiviert? Nichts ausgeben
        if (get_option('keks_plugin_enabled', '1') !== '1') {
            return;
        }

        // Nur ausgeben wenn aktiviert
        if (get_option('keks_google_consent_mode', '0') !== '1') {
            return;
        }

        // Im Admin nicht ausgeben
        if (is_admin()) {
            return;
        }
        ?>
        <script>
        // Google Consent Mode v2 - Default (vor allen Google-Scripts)
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        // Default: Alles verweigert
        gtag('consent', 'default', {
            'ad_storage': 'denied',
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'analytics_storage': 'denied',
            'wait_for_update': 500
        });

        // Bestehenden Consent aus localStorage prüfen
        (function() {
            try {
                var stored = localStorage.getItem('keks_consent');
                if (stored) {
                    var data = JSON.parse(stored);
                    if (data.categories && data.expires && new Date(data.expires) > new Date()) {
                        var consent = {
                            'ad_storage': data.categories.marketing ? 'granted' : 'denied',
                            'ad_user_data': data.categories.marketing ? 'granted' : 'denied',
                            'ad_personalization': data.categories.marketing ? 'granted' : 'denied',
                            'analytics_storage': data.categories.statistics ? 'granted' : 'denied'
                        };
                        gtag('consent', 'update', consent);
                    }
                }
            } catch(e) {}
        })();
        </script>
        <?php
    }

    /**
     * Verwaltete Scripts im Head ausgeben
     */
    public function render_managed_scripts_head() {
        $this->render_managed_scripts('head');
    }

    /**
     * Verwaltete Scripts im Footer ausgeben
     */
    public function render_managed_scripts_footer() {
        $this->render_managed_scripts('footer');
    }

    /**
     * Verwaltete Scripts ausgeben (intern)
     */
    private function render_managed_scripts($position) {
        // Plugin deaktiviert? Keine Scripts ausgeben
        if (get_option('keks_plugin_enabled', '1') !== '1') {
            return;
        }

        $scripts = get_option('keks_managed_scripts', []);
        if (!is_array($scripts) || empty($scripts)) {
            return;
        }

        $known_services = $this->get_known_services();

        foreach ($scripts as $script) {
            $service_key = $script['service'] ?? 'manual';

            // Bekannter Dienst
            if ($service_key !== 'manual' && isset($known_services[$service_key])) {
                $service = $known_services[$service_key];

                // Nur Scripts für diese Position
                if ($service['position'] !== $position) {
                    continue;
                }

                // Leere ID überspringen
                if (empty($script['service_id'])) {
                    continue;
                }

                $category = $service['category'];
                $id = $script['service_id'];
                $id2 = $script['service_id2'] ?? '';

                // Ist es ein Iframe?
                if (!empty($service['is_iframe'])) {
                    $iframe_url = str_replace('{ID}', $id, $service['iframe_template']);
                    echo keks_iframe($category, $iframe_url, ['width' => '100%', 'height' => '400', 'frameborder' => '0']) . "\n";
                    continue;
                }

                // Scripts aus Template generieren
                if (!empty($service['scripts'])) {
                    foreach ($service['scripts'] as $tpl) {
                        $content = str_replace('{ID}', $id, $tpl['template']);
                        $content = str_replace('{ID2}', $id2, $content);

                        if ($tpl['type'] === 'url') {
                            // Zusätzliche Attribute?
                            $extra_attrs = '';
                            if (!empty($tpl['attrs'])) {
                                $extra_attrs = ' ' . str_replace('{ID}', esc_attr($id), $tpl['attrs']);
                            }
                            echo '<script type="text/plain" data-keks-category="' . esc_attr($category) . '" src="' . esc_url($content) . '"' . $extra_attrs . '></script>' . "\n";
                        } else {
                            echo keks_script($category, '', $content) . "\n";
                        }
                    }
                }
            } else {
                // Manuelles Script
                // Nur Scripts für diese Position
                if (($script['position'] ?? 'footer') !== $position) {
                    continue;
                }

                // Leere Scripts überspringen
                if (empty($script['content'])) {
                    continue;
                }

                $category = $script['category'] ?? 'statistics';

                if (($script['type'] ?? 'url') === 'url') {
                    echo keks_script($category, $script['content']) . "\n";
                } else {
                    echo keks_script($category, '', $script['content']) . "\n";
                }
            }
        }
    }
}

function keks() {
    return Keks::instance();
}

// Plugin initialisieren
add_action('plugins_loaded', 'keks');

// Aktivierungs-Hook für Datenbank-Tabelle
register_activation_hook(__FILE__, ['Keks', 'activate']);

/**
 * Helper-Funktion: Script-Tag mit Blocking
 *
 * @param string $category Die Cookie-Kategorie (z.B. 'statistics', 'marketing')
 * @param string $src Optional: Externe Script-URL
 * @param string $inline Optional: Inline-JavaScript-Code
 * @return string Das HTML für das blockierte Script
 *
 * Beispiel externe URL:
 * echo keks_script('statistics', 'https://www.googletagmanager.com/gtag/js?id=G-XXXXX');
 *
 * Beispiel Inline:
 * echo keks_script('statistics', '', "gtag('config', 'G-XXXXX');");
 */
function keks_script($category, $src = '', $inline = '') {
    $attrs = 'type="text/plain" data-keks-category="' . esc_attr($category) . '"';

    if ($src) {
        return '<script ' . $attrs . ' src="' . esc_url($src) . '"></script>';
    }

    if ($inline) {
        return '<script ' . $attrs . '>' . $inline . '</script>';
    }

    return '';
}

/**
 * Helper-Funktion: Iframe mit Blocking (z.B. YouTube, Google Maps)
 *
 * @param string $category Die Cookie-Kategorie
 * @param string $src Die Iframe-URL
 * @param array $attrs Zusätzliche Attribute
 * @return string Das HTML für den blockierten Iframe
 *
 * Beispiel:
 * echo keks_iframe('marketing', 'https://www.youtube.com/embed/VIDEO_ID', [
 *     'width' => '560',
 *     'height' => '315',
 *     'frameborder' => '0'
 * ]);
 */
function keks_iframe($category, $src, $attrs = []) {
    $attr_string = 'data-keks-category="' . esc_attr($category) . '" data-keks-src="' . esc_url($src) . '"';

    foreach ($attrs as $key => $value) {
        $attr_string .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
    }

    // Placeholder message
    $placeholder = '<div style="background:#f0f0f0;padding:40px;text-align:center;border:1px solid #ddd;">';
    $placeholder .= '<p style="margin:0;">' . sprintf(esc_html(keks_t('iframe_placeholder')), esc_html($category)) . '</p>';
    $placeholder .= '</div>';

    return '<iframe ' . $attr_string . '></iframe>' . $placeholder;
}
