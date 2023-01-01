<?php
session_start();
include "assets/fetch/fetch.php";
$logindata = json_decode(file_get_contents("assets/config/auth.json"), true);

if($logindata['license_key'] == "" || $logindata['customer_id'] == ""){
header("Location: installer.php");
    exit;
}

// ini_set('display_errors', 0);
$data = fetch($logindata['customer_id'], $logindata['license_key']);
$services = json_decode($data, true);
$title = $services['title'];
// echo $title;
unset($services['title']);
ini_set('memory_limit', '80M');
$result = array();
if($services == null || !count($services) > 0 || isset($services[""]) && $services[""] == null){
    $result['error'] = "No services found";
    $result['status'] = "500";
    $result['message'] = "You have no assigned services";
    echo json_encode($result);
}else{
foreach ($services as $service) {
    if ($service[0]['host']['protocol'] == "tcp") {
        $result['tcp'][$service[0]['host']['id']][] = $service;
    }
    if ($service[0]['host']['protocol'] == "http") {
        $result['http'][$service[0]['host']['id']][] = $service;
    }
}
// echo json_encode($services);


echo "<script>console.log('" . json_encode($result) . "')</script>";

$items = array();
$outage = false;

/*foreach ($result as $item) {
    foreach ($item as $item2) {
        foreach ($item2[0] as $item3){
            if ($item3['success'] == false) {
                $outage = true;
            }
            if ($item3['host']['protocol'] == "http") {
                $items['http'][] = $item;
            }
            if ($item3['host']['protocol'] == "tcp") {
                $items['tcpip'][] = $item;
            }
        }

    }
    // echo $item['host']['protocol'];
}*/
$items = $result;
// echo "<script>console.log('" . json_encode($items) . "')</script>";

/*foreach ($result as $item) {
    echo json_encode($item);
    $item = $item[0];
    foreach ($item as $item2) {
        if ($item['success'] == false) {
            $outage = true;
        }
        if ($item['host']['protocol'] == "http") {
            $items['http'][] = $item;
        }
        if ($item['host']['protocol'] == "tcp") {
            $items['tcpip'][] = $item;
        }
    }

}*/


$checkmark = '<svg fill="none" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg" class="service-status-icon ok green">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="m53.649 28.688-19.476 19.62c-.846.652-1.665.98-2.461.98h-1.688c-.46-.005-.913-.1-1.336-.278a3.188 3.188 0 0 1-1.125-.703l-9.212-9.283a1.54 1.54 0 0 1-.35-1.022c0-.396.116-.738.35-1.017l3.659-3.59A1.157 1.157 0 0 1 23 32.9c.418 0 .765.166 1.053.495l5.827 5.845a1.44 1.44 0 0 0 2.043-.005l16.029-16.177c.318-.23.7-.353 1.093-.35.387 0 .707.116.945.35l3.654 3.587c.23.315.356.697.352 1.089 0 .405-.117.72-.352.945l.005.009Zm15.502-6.638a36.171 36.171 0 0 0-7.663-11.457 36.44 36.44 0 0 0-11.493-7.695A34.69 34.69 0 0 0 36 0c-4.878 0-9.526.945-13.95 2.849a36.111 36.111 0 0 0-11.466 7.663A36.39 36.39 0 0 0 2.89 22.005 34.618 34.618 0 0 0 0 36c0 4.873.945 9.526 2.849 13.959 1.899 4.428 4.455 8.248 7.663 11.457a36.358 36.358 0 0 0 11.498 7.695A34.891 34.891 0 0 0 36.005 72c4.873 0 9.526-.945 13.959-2.849a36.31 36.31 0 0 0 11.46-7.663A36.82 36.82 0 0 0 69.12 49.99 34.978 34.978 0 0 0 72 35.995c0-4.873-.945-9.526-2.849-13.958"
                                                fill="#36B27E"></path></svg>';
$cross = '<svg width="14" height="14"
                                                                                         viewBox="0 0 14 14" fill="none"
                                                                                         xmlns="http://www.w3.org/2000/svg"
                                                                                         class="service-status-icon"><path
                                                fill-rule="evenodd" clip-rule="evenodd"
                                                d="M14 7C14 10.866 10.866 14 7 14C3.13401 14 0 10.866 0 7C0 3.13401 3.13401 0 7 0C10.866 0 14 3.13401 14 7ZM4.83904 3.91096C4.58276 3.65468 4.16724 3.65468 3.91096 3.91096C3.65468 4.16724 3.65468 4.58276 3.91096 4.83904L6.07192 7L3.91096 9.16096C3.65468 9.41724 3.65468 9.83276 3.91096 10.089C4.16724 10.3453 4.58276 10.3453 4.83904 10.089L7 7.92808L9.16096 10.089C9.41724 10.3453 9.83276 10.3453 10.089 10.089C10.3453 9.83276 10.3453 9.41724 10.089 9.16096L7.92808 7L10.089 4.83904C10.3453 4.58276 10.3453 4.16724 10.089 3.91096C9.83276 3.65468 9.41724 3.65468 9.16096 3.91096L7 6.07192L4.83904 3.91096Z"
                                                fill="#E95858"></path></svg>';

$crossbig = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"
                         class="status-icon">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M14 7C14 10.866 10.866 14 7 14C3.13401 14 0 10.866 0 7C0 3.13401 3.13401 0 7 0C10.866 0 14 3.13401 14 7ZM4.83904 3.91096C4.58276 3.65468 4.16724 3.65468 3.91096 3.91096C3.65468 4.16724 3.65468 4.58276 3.91096 4.83904L6.07192 7L3.91096 9.16096C3.65468 9.41724 3.65468 9.83276 3.91096 10.089C4.16724 10.3453 4.58276 10.3453 4.83904 10.089L7 7.92808L9.16096 10.089C9.41724 10.3453 9.83276 10.3453 10.089 10.089C10.3453 9.83276 10.3453 9.41724 10.089 9.16096L7.92808 7L10.089 4.83904C10.3453 4.58276 10.3453 4.16724 10.089 3.91096C9.83276 3.65468 9.41724 3.65468 9.16096 3.91096L7 6.07192L4.83904 3.91096Z"
                              fill="#E95858"></path>
                    </svg>';
?>


<html theme="light" style="--accent-color:#36b27e; --color-border-button:#36b27e;">
<head>
    <meta charset="utf-8">
    <meta property="og:title" content="ySenayIT">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta property="og:image" content="https://status.zeneg.de/api/og-image?name=ySenayIT&amp;favicon=">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="https://status.yseanyit.de">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $title ?>">
    <meta property="og:description" content="<?= $title ?>">
    <meta content="Find out why Lattice is trusted by the best places to work." property="og:description">
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="status.zeneg.de">
    <meta property="twitter:url" content="https://status.zeneg.de">
    <meta name="twitter:title" content="<?= $title ?>">
    <meta name="twitter:description" content="<?= $title ?>">
    <meta name="twitter:image" content="https://status.zeneg.de/api/og-image?name=ySenayIT&amp;favicon=">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"
          data-optimized-fonts="true">

    <script defer="" data-domain="status.zeneg.de" src="https://plausible.io/js/plausible.js"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '', {
            page_path: window.location.pathname,
        });
    </script>
    <meta name="next-head-count" content="21">
    <link rel="preload" href="https://status.zeneg.de/_next/static/css/9b096d759103e862.css" as="style">
    <link rel="stylesheet" href="https://status.zeneg.de/_next/static/css/9b096d759103e862.css" data-n-g="">
    <link rel="preload" href="https://status.zeneg.de/_next/static/css/a5e7ab84e5d4208e.css" as="style">
    <link rel="stylesheet" href="https://status.zeneg.de/_next/static/css/a5e7ab84e5d4208e.css" data-n-p="">
    <noscript data-n-css=""></noscript>
    <script defer="" nomodule=""
            src="https://status.zeneg.de/_next/static/chunks/polyfills-c67a75d1b6f99dc8.js"></script>
    <script src="https://status.zeneg.de/_next/static/chunks/webpack-a70bca757d115857.js" defer=""></script>
    <script src="https://status.zeneg.de/_next/static/chunks/framework-bb5c596eafb42b22.js" defer=""></script>
    <script src="https://status.zeneg.de/_next/static/chunks/main-4303a103c1f74d1d.js" defer=""></script>
    <script src="https://status.zeneg.de/_next/static/chunks/pages/_app-a4cea1267f8798f4.js" defer=""></script>
    <script src="https://status.zeneg.de/_next/static/chunks/994-e7fee8a2b9e1a819.js" defer=""></script>
    <script src="https://status.zeneg.de/_next/static/chunks/pages/_sites/%5Bsite%5D-2dcbd342099ae87b.js"
            defer=""></script>
    <script src="https://status.zeneg.de/_next/static/JqplT6QaK-0TallnYH2KL/_buildManifest.js" defer=""></script>
    <script src="https://status.zeneg.de/_next/static/JqplT6QaK-0TallnYH2KL/_ssgManifest.js" defer=""></script>
    <style id="_goober">
        @keyframes go2264125279 {
            from {
                transform: scale(0) rotate(45deg);
                opacity: 0;
            }
            to {
                transform: scale(1) rotate(45deg);
                opacity: 1;
            }
        }

        @keyframes go3020080000 {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes go463499852 {
            from {
                transform: scale(0) rotate(90deg);
                opacity: 0;
            }
            to {
                transform: scale(1) rotate(90deg);
                opacity: 1;
            }
        }

        @keyframes go1268368563 {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes go1310225428 {
            from {
                transform: scale(0) rotate(45deg);
                opacity: 0;
            }
            to {
                transform: scale(1) rotate(45deg);
                opacity: 1;
            }
        }

        @keyframes go651618207 {
            0% {
                height: 0;
                width: 0;
                opacity: 0;
            }
            40% {
                height: 0;
                width: 6px;
                opacity: 1;
            }
            100% {
                opacity: 1;
                height: 10px;
            }
        }

        @keyframes go901347462 {
            from {
                transform: scale(0.6);
                opacity: 0.4;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .go4109123758 {
            z-index: 9999;
        }

        .go4109123758 > * {
            pointer-events: auto;
        }</style>
    <style type="text/css" data-styled-jsx="">body, html {
            font-family: Inter, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, sans-serif
        }</style>
    <title><?= $title ?></title>
    <script src="/va/script.js" defer=""></script>
</head>
<body>
<script>
    let i = 0;

    document.onfocus = function () {
        window.location.reload();
    };

    setInterval(function () {
        i++;
        i++;
        i++;
        i++;
        document.getElementById('progress').value = i
        document.getElementById('progressint').innerText = i / 1000
        if (i == 60000) {
            window.location.reload();
        }
        if (!document.hidden && i == 60000) {
            window.location.reload();
        }
    }, 1);
</script>
<div id="__next" data-reactroot="">
    <div style="visibility: visible;">
        <div class="wrapper">
            <div class="section header">
                <div class="skeleton" style="height: 40px; width: 160px;"></div>
                <div class="flex align-items-center gap-2em"><a
                            class="flex align-items-center gap-05em text-color font-size-15 clickable" target="_blank"
                            href="https://ysenayit.de">ySenayIT
                        <svg width="15px" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                    <a
                            class="flex align-items-center gap-05em text-color font-size-15 clickable"
                            href="?refresh=true">Jetzt neu laden
                        <svg width="15px" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="section current-status status-banner">
                <div class="top-level-status">
                    <?php if ($outage) {
                        echo $crossbig;
                    } else {
                        echo "<span style='width: 5%; height: 5%'>$checkmark</span>";
                    } ?>
                    <div>
                        <div class="status-title outage" style="max-width: 100%;"><?php if ($outage) {
                                echo "Service outage";
                            } else {
                                echo "All systems up";
                            } ?></div>
                    </div>
                </div>
            </div>
            <div class="section services ">
                <!-- TCPIP -->
                <?php if (isset($items['tcp']) && count($items['tcp']) > 0) { ?>
                    <div class="block-container">
                        <div class="Tooltip-wrapper"
                             style="transform: translate3d(calc(275px - 50% + 6px), 36px, 0px);">
                            <div class="Tooltip bottom" hidden>
                                <div class="snapshot-tooltip">
                                    <div class="snapshot-tooltip-header"><span>-----</span></div>
                                    <div>No data</div>
                                </div>
                            </div>
                        </div>
                        <div class="block-name">TCP</div>
                        <div class="section block">
                            <?php
                            // echo json_encode($items['tcpip']);

                            foreach ($items['tcp'] as $itm) {
                                // echo json_encode($itm);

                                ?>
                                <div class="service-row ">
                                    <div class="service-header "><span class="service-name">
                                        <?php
                                        /*                                        $outage = false;
                                                                                foreach ($itm[0] as $checkstatus) {
                                                                                    if ($checkstatus['status'] == "offline" || !$checkstatus['success']) {
                                                                                        $outage = true;
                                                                                        break;
                                                                                    }
                                                                                }
                                                                                echo $outage;

                                                                                */ ?><!--
                                        --><?php /*if ($outage) {
                                            echo $checkmark;
                                        } else {
                                            echo $cross;
                                        }
                                        $outage = false;
                                        */ ?>



                                            <?php echo $itm[0][0]['host']['name']; ?></span>
                                        </span>
                                    </div>
                                    <div style="position: relative; width: 100%;">
                                        <div class="snapshots style1">

                                            <?php


                                            // $servicehistory = json_decode(file_get_contents("assets/config/services/" . $itm['host']['id'] . ".json"), true);
                                            $servicehistory = $itm[0];
                                            if (count($servicehistory) > 80) {
                                                $servicehistory = array_slice($servicehistory, count($servicehistory) - 80, 80);
                                            }
                                            // echo json_encode($servicehistory);
                                            $i = 0;

                                            foreach ($servicehistory as $item) {
                                                // echo json_encode($item);
                                                /*switch ($item['status']) {
                                                    case "online":
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(54,178,126);"></div></a>';
                                                        break;
                                                    case "offline":
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,0,0);"></div></a>';
                                                        break;
                                                    case "dns_not_found":
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,112,22);"></div></a>';
                                                        break;
                                                    default:
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="snapshot no-data"></div></a>';
                                                }*/
                                                switch ($item['status']) {
                                                    case "online":
                                                        echo '<div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(54,178,126);"></div>';
                                                        break;
                                                    case "offline":
                                                        echo '<div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,0,0);"></div>';
                                                        break;
                                                    case "dns_not_found":
                                                        echo '<div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,112,22);"></div>';
                                                        break;
                                                    default:
                                                        echo '<div class="snapshot no-data"></div>';
                                                }
                                            }

                                            // <div class="snapshot" style="background: rgb(233, 88, 88);"></div> | online
                                            // <div class="snapshot" style="background: rgb(54,178,126);"></div> | offline
                                            // <div class="snapshot" style="background: rgb(255,112,22);"></div> | dns_not_found
                                            // <div class="snapshot no-data"></div> | no data

                                            $seconds = $servicehistory[count($servicehistory) - 2]['datetime'] - $servicehistory[count($servicehistory) - 3]['datetime'];
                                            ?>


                                        </div>
                                        <div class="flex justify-content-space-between uptime-periods">
                                            <span>Last 50 entrys</span><span>Every <?php echo gmdate("H:i:s", $seconds); ?>h</span><span>Now</span>
                                        </div>
                                    </div>
                                </div>


                            <?php }

                            ?>
                        </div>
                    </div>
                <?php } ?>
                <!-- WEB -->
                <?php if (isset($items['http']) && count($items['http']) > 0) { ?>
                    <div class="block-container">
                        <div class="Tooltip-wrapper"
                             style="transform: translate3d(calc(275px - 50% + 6px), 36px, 0px);">
                            <div class="Tooltip bottom" hidden>
                                <div class="snapshot-tooltip">
                                    <div class="snapshot-tooltip-header"><span>-----</span></div>
                                    <div>No data</div>
                                </div>
                            </div>
                        </div>
                        <div class="block-name">WEB</div>
                        <div class="section block">
                            <?php
                            // echo json_encode($items['tcpip']);

                            foreach ($items['http'] as $itm) {
                                // echo json_encode($itm);

                                ?>
                                <div class="service-row ">
                                    <div class="service-header "><span class="service-name">
                                        <?php
                                        /*                                        $outage = false;
                                                                                foreach ($itm[0] as $checkstatus) {
                                                                                    if ($checkstatus['status'] == "offline" || !$checkstatus['success']) {
                                                                                        $outage = true;
                                                                                        break;
                                                                                    }
                                                                                }
                                                                                echo $outage;

                                                                                */ ?><!--
                                        --><?php /*if ($outage) {
                                            echo $checkmark;
                                        } else {
                                            echo $cross;
                                        }
                                        $outage = false;
                                        */ ?>



                                            <?php echo $itm[0][0]['host']['name']; ?></span>
                                        </span>
                                    </div>
                                    <div style="position: relative; width: 100%;">
                                        <div class="snapshots style1">

                                            <?php


                                            // $servicehistory = json_decode(file_get_contents("assets/config/services/" . $itm['host']['id'] . ".json"), true);
                                            $servicehistory = $itm[0];
                                            if (count($servicehistory) > 80) {
                                                $servicehistory = array_slice($servicehistory, count($servicehistory) - 80, 80);
                                            }
                                            // echo json_encode($servicehistory);
                                            $i = 0;

                                            foreach ($servicehistory as $item) {
                                                // echo json_encode($item);
                                                /*switch ($item['status']) {
                                                    case "online":
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(54,178,126);"></div></a>';
                                                        break;
                                                    case "offline":
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,0,0);"></div></a>';
                                                        break;
                                                    case "dns_not_found":
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,112,22);"></div></a>';
                                                        break;
                                                    default:
                                                        echo '<a onmouseover="showInfo("'.$item['host']['id'].'")"><div class="snapshot no-data"></div></a>';
                                                }*/
                                                switch ($item['status']) {
                                                    case "online":
                                                        echo '<div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(54,178,126);"></div>';
                                                        break;
                                                    case "offline":
                                                        echo '<div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,0,0);"></div>';
                                                        break;
                                                    case "dns_not_found":
                                                        echo '<div class="servicestatuselement snapshot" title="' . date("d.m.Y | H:i:s", $item['datetime']) . ' " style="background: rgb(255,112,22);"></div>';
                                                        break;
                                                    default:
                                                        echo '<div class="snapshot no-data"></div>';
                                                }
                                            }

                                            // <div class="snapshot" style="background: rgb(233, 88, 88);"></div> | online
                                            // <div class="snapshot" style="background: rgb(54,178,126);"></div> | offline
                                            // <div class="snapshot" style="background: rgb(255,112,22);"></div> | dns_not_found
                                            // <div class="snapshot no-data"></div> | no data

                                            $seconds = $servicehistory[count($servicehistory) - 2]['datetime'] - $servicehistory[count($servicehistory) - 3]['datetime'];
                                            ?>


                                        </div>
                                        <div class="flex justify-content-space-between uptime-periods">
                                            <span>Last 50 entrys</span><span>Every <?php echo gmdate("H:i:s", $seconds); ?>h</span><span>Now</span>
                                        </div>
                                    </div>
                                </div>


                            <?php }
                            ?>
                        </div>
                    </div>
                <?php } ?>


                <!-- Reload Timer -->
                <div class="block-container">
                    <div class="block-name">Reload in</div>
                    <div class="section block">
                        <progress id="progress" value="0" max="60000"></progress>
                        <span id="progressint"></span><span>s /60s</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="section footer">
            <div class="flex justify-content-space-between align-items-center width-100 sm-footer"
                 style="margin-left: 1em; margin-right: 1em;">
                <div><a class="powered-by text-color text-decoration-none clickable" target="_blank"
                        rel="noopener noreferrer"
                        href="https://ysenayit.de">Powered
                        by <span class="color-text-blue">ySenayIT.DE</span></a></div>
            </div>
        </div>
    </div>
</div>


<script>
    function showInfo(id) {
        console.log('<?php json_encode($items)?>')
        // let data = JSON.parse('<?php echo json_encode($items); ?>');
        // console.log(data)
    }
</script>

<next-route-announcer><p aria-live="assertive" id="__next-route-announcer__" role="alert"
                         style="border: 0px; clip: rect(0px, 0px, 0px, 0px); height: 1px; margin: -1px; overflow: hidden; padding: 0px; position: absolute; width: 1px; white-space: nowrap; overflow-wrap: normal;"></p>
</next-route-announcer>
<div class="hpd-toast-wrapper"></div>
<svg aria-hidden="true" style="width: 0px; height: 0px; position: absolute; top: -100%; left: -100%;">
    <text id="__react_svg_text_measurement_id">09</text>
</svg>
</body>
</html>
<?php }