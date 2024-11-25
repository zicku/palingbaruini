<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Home Page</title>
  <meta name="description" content="Monitoring Journal">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="apple-touch-icon" href="images/uinws.png">
  <link rel="shortcut icon" href="images/uinws.png">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
  <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/weathericons@2.1.0/css/weather-icons.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>
  <script type="text/javascript" src="assets/js/pdf/main.js"></script>
</head>

<body>
  <aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
      <div id="main-menu" class="main-menu collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="Halaman_Home.php"><i class="menu-icon fa fa-home"></i>Home</a></li>
          <li><a href="Halaman_Jurnal.php"><i class="menu-icon fa fa-laptop"></i>Journal Page</a></li>
          <li><a href="Halaman_Artikel.php"><i class="menu-icon fa fa-book"></i>Article Page</a></li>
        </ul>
      </div>
    </nav>
  </aside>

  <div id="right-panel" class="right-panel">
    <header id="header" class="header">
      <div class="top-left">
        <div class="navbar-header">
          <a class="navbar-brand" href=""><img src="images/uinws (2).png" alt="Logo"></a>
          <a class="navbar-brand hidden" href=""><img src="images/uinws (2).png" alt="Logo"></a>
          <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
        </div>
      </div>
    </header>

    <div class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="box-title text-center">Information</h3>
            </div>
            <div class="card-body">
              <p class="card-text">Information about the Monitoring System:<br><br>
                1. The Web Scraper script checks for journal updates automatically every hour.<br>
                2. The Check Update button on the Home page allows users to manually check for updates.<br>
                3. The Check Update button runs the Web Scraper script in the background (duration approximately 5-10 minutes).<br>
                4. The Check Update process continues even if the website page is closed.<br>
                5. The ongoing Check Update process is indicated by a running refresh symbol.<br>
                6. The Check Update progress bar will fill up when the script completes and the Home page remains open.<br>
                7. The Check Update progress bar will not fill if the Home page is replaced with another page.<br>
                8. Article data is displayed in alphabetical order.<br>
                9. If article data does not display automatically when the filter function is used, please return to page 1.<br>
                10. If a "Fatal Error" occurs while displaying article data, it means the system is running the Web Scraper script. Please refresh the Article Page when this happens.
              </p><br>

              <?php
              if (isset($_POST['checkUpdate'])) {
                $descriptorspec = [
                  0 => ["pipe", "r"],
                  1 => ["pipe", "w"],
                  2 => ["pipe", "w"]
                ];
                
                $process = proc_open('/home/monito29/virtualenv/public_html/jurnalscraper/3.10/bin/python3 JurnalScraper.py &', $descriptorspec, $pipes);
                
                if (is_resource($process)) {
                    fclose($pipes[0]);
                    $output = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    $error = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);
                }
              }
              ?>
              <div class="progress mb-3">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo isset($_POST['checkUpdate']) ? '100%' : '0%'; ?>" aria-valuenow="<?php echo isset($_POST['checkUpdate']) ? '100' : '0'; ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>

              <div class="row mt-4">
                <div class="col-md-12 d-flex justify-content-center">
                  <form action="" method="post">
                    <button type="submit" class="btn button-78" name="checkUpdate" id="checkUpdateBtn">Check Update</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="clearfix"></div>
      <footer class="site-footer">
        <div class="footer-inner bg-white">
          <div class="row">
            <div class="col-sm-6">
              Copyright &copy; 2024 Hasyim Yahya
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simpleweather@3.1.0/jquery.simpleWeather.min.js"></script>
  <script src="assets/js/init/weather-init.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
  <script src="assets/js/init/fullcalendar-init.js"></script>
</body>

</html>
