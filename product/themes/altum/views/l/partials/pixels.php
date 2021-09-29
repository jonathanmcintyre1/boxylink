<?php defined('ALTUMCODE') || die() ?>

<?php foreach($data->pixels as $pixel): ?>

    <?php if($pixel->type == 'facebook'): ?>
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '<?= $pixel->pixel ?>');
            fbq('track', 'PageView');
        </script>

        <noscript>
            <img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id=<?= $pixel->pixel ?>&ev=PageView&noscript=1"/>
        </noscript>
    <?php endif ?>

    <?php if($pixel->type == 'google_analytics'): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $pixel->pixel ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '<?= $pixel->pixel ?>');
        </script>
    <?php endif ?>

    <?php if($pixel->type == 'google_tag_manager'): ?>
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?= $pixel->pixel ?>');
        </script>

        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?= $pixel->pixel ?>" height="0" width="0" style="display: none; visibility: hidden;"></iframe>
        </noscript>
    <?php endif ?>

    <?php if($pixel->type == 'linkedin'): ?>
        <script type="text/javascript">
            _linkedin_data_partner_id = "<?= $pixel->pixel ?>";
        </script>

        <script type="text/javascript">
            (function(){var s = document.getElementsByTagName("script")[0];
                var b = document.createElement("script");
                b.type = "text/javascript";b.async = true;
                b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
                s.parentNode.insertBefore(b, s);})();
        </script>

        <noscript>
            <img height="1" width="1" style="display: none;" alt="" src="https://dc.ads.linkedin.com/collect/?pid=<?= $pixel->pixel ?>&fmt=gif" />
        </noscript>

    <?php endif ?>

    <?php if($pixel->type == 'pinterest'): ?>
        <script type="text/javascript">
            !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version="3.0";var t=document.createElement("script");t.async=!0,t.src=e;var r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
            pintrk('load', '<?= $pixel->pixel ?>');
            pintrk('page');
        </script>

        <noscript>
            <img height="1" width="1" style="display:none;" alt=""
                 src="https://ct.pinterest.com/v3/?tid=<?= $pixel->pixel ?>&noscript=1" />
        </noscript>
    <?php endif ?>

    <?php if($pixel->type == 'twitter'): ?>
        <script>
            !function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
            },s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='//static.ads-twitter.com/uwt.js',
                a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');

            twq('init', '<?= $pixel->pixel ?>');
            twq('track', 'PageView');
        </script>
    <?php endif ?>

    <?php if($pixel->type == 'quora'): ?>
        <script>
            !function(q,e,v,n,t,s){if(q.qp) return; n=q.qp=function(){n.qp?n.qp.apply(n,arguments):n.queue.push(arguments);}; n.queue=[];t=document.createElement(e);t.async=!0;t.src=v; s=document.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s);}(window, 'script', 'https://a.quora.com/qevents.js');
            qp('init', '<?= $pixel->pixel ?>');
            qp('track', 'ViewContent');
        </script>

        <noscript>
            <img height="1" width="1" style="display: none;" src="https://q.quora.com/_/ad/<?= $pixel->pixel ?>/pixel?tag=ViewContent&noscript=1"/>
        </noscript>
    <?php endif ?>

<?php endforeach ?>


